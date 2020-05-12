<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ProfileModel;
use App\Models\SearchModel;
use App\Models\UserModel;
use App\Session\Session;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SearchController extends Controller
{
    public function search(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();

        $longitude = $request->getParam('longitude');
        $latitude = $request->getParam('latitude');
        $index = $request->getParam('index');
        $age_range = $request->getParam('age');
        $score_range = $request->getParam('score');
        $distance_range = $request->getParam('distance');
        $user_interests = $request->getParam('interest');

        if (!Validator::check_array($index) || !Validator::check_array($age_range) || !Validator::check_array($score_range)
            || !Validator::check_array($distance_range) || !Validator::check_array($user_interests)
            || !Validator::check_array($longitude) || !Validator::check_array($latitude)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        if (!Validator::check_int($index)) {
            $index = 0;
        }

        // Default range of (score , age and distance)
        if (!isset($distance_range) || !Validator::check_int($distance_range)) {
            $distance_range = 200;
        }

        if (!isset($score_range) || !Validator::check_int($score_range)) {
            $score_range = 150;
        }

        if (!isset($age_range) || !Validator::check_int($age_range)) {
            $age_range = 25;
        }
        // Get user interests from GET paramas for search
        $user_interests = trim($user_interests);
        if (isset($user_interests)) {
            if (!Validator::check_array($user_interests)) {
                array_push($error, "Invalid Input !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            } else {
                $user_interests = explode("#", $user_interests);
                unset($user_interests[0]);
                $user_interests = array_values($user_interests);
            }
        }

        // Select data
        Session::start();
        $username = Session::unserialize("user")["username"];
        if (!isset($username) || empty(trim($username))) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        // Get connected user data
        $profileModel = new ProfileModel();
        $res = $profileModel->get_user_profile($pdo, $username);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $user_id = $res[0][0];
        $user_orientation = $res[0][8];
        $user_gender = $res[0][7];
        $user_latitude = $res[0][15];
        $user_longitude = $res[0][16];

        if (Validator::check_latitude($latitude) && Validator::check_longitude($longitude)) {
            $user_latitude = $latitude;
            $user_longitude = $longitude;
        }

        $user_birthdate = $res[0][6];
        $user_score = $res[0][12];
        // Get connected user interests
        if (count($user_interests) < 1) {
            $res = $profileModel->get_user_interests($pdo, $user_id);
            if ($res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
            $user_interests = array();
            for ($i = 0; $i < count($res); $i++) {
                array_push($user_interests, $res[$i][0]);
            }
        }
        // Orientation management
        $wanted_gender = 2;
        if ($user_orientation == 0) {
            $wanted_gender = 0;
        } else if ($user_orientation == 1) {
            $wanted_gender = 1;
        } else {
            $wanted_gender = 2;
        }
        // Get users by interest
        $searchModel = new SearchModel();
        $users_res = array();
        for ($i = 0; $i < count($user_interests); $i++) {
            $res = $searchModel->get_interest_users($pdo, $user_interests[$i]);
            if ($res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
            $users_res = array_merge($users_res, $res);
        }
        $users_res = array_unique($users_res, SORT_REGULAR);
        $res = $searchModel->get_no_interest_users($pdo);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        // Merge no interest users
        //$users_res = array_merge($users_res, $res);
        // Get interests of each user
        for ($i = 0; $i < count($users_res); $i++) {
            $res = $profileModel->get_user_interests($pdo, (int) $users_res[$i][0]);
            if ($res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
            $current_user_interests = array();
            for ($j = 0; $j < count($res); $j++) {
                array_push($current_user_interests, $res[$j][0]);
            }
            array_push($users_res[$i], count($current_user_interests));
            if (empty($current_user_interests)) {
                $current_user_interests = null;
            }
            array_push($users_res[$i], $current_user_interests);
        }
        // Remove connected user
        for ($i = 0; $i < count($users_res); $i++) {
            if (isset($users_res[$i])) {
                if ($users_res[$i][0] === $user_id) {
                    $users_res[$i] = null;
                    unset($users_res[$i]);
                    $users_res = array_values($users_res);
                    $i--;
                }
            }
        }
        // Remove unselected gender
        if ($wanted_gender === 1) {
            for ($i = 0; $i < count($users_res); $i++) {
                if (isset($users_res[$i])) {
                    if ($users_res[$i][7] === "0") {
                        $users_res[$i] = null;
                        unset($users_res[$i]);
                        $users_res = array_values($users_res);
                        $i--;
                    }
                }
            }
        } else if ($wanted_gender === 0) {
            for ($i = 0; $i < count($users_res); $i++) {
                if (isset($users_res[$i])) {
                    if ($users_res[$i][7] === "1") {
                        $users_res[$i] = null;
                        unset($users_res[$i]);
                        $users_res = array_values($users_res);
                        $i--;
                    }
                }
            }
        }

        // Remove users with orientation different from the user's
        for ($i = 0; $i < count($users_res); $i++) {
            if (isset($users_res[$i])) {
                if ($users_res[$i][8] != $user_gender && $users_res[$i][8] != "2") {
                    $users_res[$i] = null;
                    unset($users_res[$i]);
                    $users_res = array_values($users_res);
                    $i--;
                }
            }
        }
        // Remove users with age range bigger than the selected one
        for ($i = 0; $i < count($users_res); $i++) {
            if (isset($users_res[$i])) {
                if ($this->year_diff($users_res[$i][6], $user_birthdate) > $age_range) {
                    $users_res[$i] = null;
                    unset($users_res[$i]);
                    $users_res = array_values($users_res);
                    $i--;
                }
            }
        }
        // Remove users with score range bigger than the selected one
        for ($i = 0; $i < count($users_res); $i++) {
            if (isset($users_res[$i])) {
                if (abs($users_res[$i][12] - $user_score) > $score_range) {
                    $users_res[$i] = null;
                    unset($users_res[$i]);
                    $users_res = array_values($users_res);
                    $i--;
                }
            }
        }
        // Storing distance to users
        for ($i = 0; $i <= count($users_res); $i++) {
            if (isset($users_res[$i])) {
                $distance = $this->getDistance($pdo, $user_latitude, $user_longitude, $users_res[$i][15], $users_res[$i][16]);
                if ($distance === false) {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                }
                array_push($users_res[$i], $distance);
            }
        }
        // Remove users far from the connected user
        for ($i = 0; $i < count($users_res); $i++) {
            if (isset($users_res[$i])) {
                if ($users_res[$i][20] > $distance_range) {
                    $users_res[$i] = null;
                    unset($users_res[$i]);
                    $users_res = array_values($users_res);
                    $i--;
                }
            }
        }
        $userModel = new UserModel();
        // Remove blocked users
        for ($i = 0; $i < count($users_res); $i++) {
            if (isset($users_res[$i])) {
                $res = $userModel->get_user_block_link_count($pdo, $user_id, $users_res[$i][0]);
                if ($res === false) {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                } else if ((int) $res === 1) {
                    $users_res[$i] = null;
                    unset($users_res[$i]);
                    $users_res = array_values($users_res);
                    $i--;
                }
            }
        }
        $users_res = array_unique($users_res, SORT_REGULAR);
        // Sort by distance
        for ($i = 0; $i < count($users_res); $i++) {
            for ($j = $i + 1; $j < count($users_res); $j++) {
                if (isset($users_res[$i]) && isset($users_res[$j])) {
                    if ($users_res[$j][20] < $users_res[$i][20]) {
                        $temp = $users_res[$i];
                        $users_res[$i] = $users_res[$j];
                        $users_res[$j] = $temp;
                    }
                }

            }
        }
        // Remove null
        //$users_res = array_filter($users_res, function ($value) {return !is_null($value) && $value !== '';});
        // Loading Number of User per page
        $upp = 5;
        $users_count = count($users_res);
        if ($index < 0) {
            $index = 0;
        }

        // Get the final result
        $res = array();
        //error_log(print_r("count is " . $users_count . " from " . $index . " to " . ($index + $upp), true));
        for ($i = $index; $i < count($users_res); $i++) {
            if (count($res) === $upp) {
                break;
            }
            if (isset($users_res[$i])) {
                //error_log(print_r("pushed index " . $i, true));
                array_push($res, $users_res[$i]);
            }
        }
        array_push($data, $res);
        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }

    public function year_diff($date1, $date2)
    {
        $date1 = date_create($date1);
        $date2 = date_create($date2);
        $diff = date_diff($date1, $date2)->y;
        return $diff;
    }

    public function getDistance($pdo, $lat1, $lon1, $lat2, $lon2)
    {
        /* $R = 6371; // Radius of the earth in km
        $dLat = deg2rad($lat2 - $lat1); // deg2rad below
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = $R * $c; // Distance in km
        return $d; */

        $searchModel = new SearchModel();
        $distance = $searchModel->get_distance($pdo, $lat1, $lon1, $lat2, $lon2) / 1000;
        return $distance;
    }

    public function sortArray($array, $column)
    {
        usort($array, function ($a, $b) {
            return $a[$column] - $b[$column];
        });
    }
}
