<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ProfileModel;
use App\Models\RegisterModel;
use App\Models\ScoreModel;
use App\Models\UserModel;
use App\Session\Session;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \DateTime;

class UserController extends Controller
{
    public function render_profile(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        Session::start();
        $username = $request->getParam('username');
        if (!isset($username) || !Validator::check_array($username)) {
            $username = Session::unserialize("user")["username"];
        }
        $registerModel = new RegisterModel();
        $username_count = $registerModel->username_count($pdo, $username);
        if ($username_count !== 1) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

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
        if (!isset($username) || empty(trim($username))) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $id = $res[0][0];
        $password = $res[0][2];
        $email = $res[0][3];
        $firstname = $res[0][4];
        $lastname = $res[0][5];
        $age = $res[0][6];
        $date = new DateTime($age);
        $now = new DateTime();
        $age = $date->diff($now)->format("%y");

        $gender = $res[0][7];
        $orientation = $res[0][8];
        $bio = $res[0][11];
        $score = $res[0][12];
        $last_connection = $res[0][13];
        $online = $res[0][14];
        $longitude = $res[0][16];
        $latitude = $res[0][15];
        $avatar = $res[0][17];

        // Interest
        $connected_username = $username;
        $res = $profileModel->get_user_profile($pdo, $connected_username);
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
        // Get like, report, block data
        // Get connected user data
        Session::start();
        $connected_username = Session::unserialize("user")["username"];
        $res = $profileModel->get_user_profile($pdo, $connected_username);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        if (!isset($res[0][0])) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $connected_user_id = $res[0][0];
        $selected_user_id = $id;

        $userModel = new UserModel();
        $res = $userModel->get_user_like_link_count($pdo, $connected_user_id, $selected_user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $like_link = $res;

        $res = $userModel->get_user_like_link_count($pdo, $selected_user_id, $connected_user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $liked_link = $res;

        $res = $userModel->get_user_report_link_count($pdo, $connected_user_id, $selected_user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $report_link = $res;
        $res = $userModel->get_user_block_link_count($pdo, $connected_user_id, $selected_user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $block_link = $res;
        if ($username !== $connected_username) {
            // Check if user is blocked by the visited one
            $check_block_res = $userModel->get_user_block_link_count($pdo, $user_id, $connected_user_id);
            if ($check_block_res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
            $scoreModel = new ScoreModel();
            $score_res = $scoreModel->update_score($pdo, $selected_user_id, 1);
            $res = true;
            if ((int) $check_block_res === 0) {
                $res = $userModel->add_notification($pdo, $connected_user_id, $selected_user_id, "visit");
            }

            if ($res === false || $score_res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }

        }
        $user_profile = array("username" => $username, "firstname" => $firstname, "lastname" => $lastname, "email" => $email, "age" => $age, "gender" => $gender, "bio" => $bio, "orientation" => $orientation, "score" => $score, "latitude" => $latitude, "longitude" => $longitude, "avatar" => $avatar, "interest" => $user_interests, "like" => $like_link, "report" => $report_link, "block" => $block_link, "last_connection" => $last_connection, "online" => $online, "liked" => $liked_link);
        $user_profile = json_encode($user_profile);

        return $this->render($response, "user.twig", array("user_profile" => $user_profile));
    }

    public function link_profile(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();

        $action = $request->getParam("action");
        $selected_username = $request->getParam("username");
        Session::start();
        $connected_username = Session::unserialize("user")["username"];

        if (!Validator::check_array($selected_username) || !Validator::check_array($action)) {
            array_push($error, "Input error !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Check if the user is the same
        if ($connected_username === $selected_username) {
            $status = Status::BAD_REQUEST;
            array_push($error, "You can't auto (Like, Report or Block) yourself !");

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Check username
        $registerModel = new RegisterModel();
        $profileModel = new ProfileModel();
        $userModel = new UserModel();
        $scoreModel = new ScoreModel();

        $res = $registerModel->username_count($pdo, $selected_username);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        } else {
            if ($res === 0) {
                array_push($error, "No user with this username exists !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            } else {
                // Get users ids
                $res = $profileModel->get_user_profile($pdo, $connected_username);
                if ($res === false) {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                }
                $connected_user_id = $res[0][0];
                $connected_user = $res;

                $res = $profileModel->get_user_profile($pdo, $selected_username);
                if ($res === false) {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                }
                $selected_user_id = $res[0][0];
                // Check if the connected user is blocked by the selected one
                $check_block_res = $userModel->get_user_block_link_count($pdo, $selected_user_id, $connected_user_id);
                if ($check_block_res === false) {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                }

                // Like, Block, Report management
                if ($action === "like") {
                    // Check if user has a completed profile
                    $bio = $connected_user[0][11];
                    $latitude = $connected_user[0][15];
                    $longitude = $connected_user[0][16];
                    $avatar = $connected_user[0][17];
                    if ($bio === null || $avatar === null || $longitude === null || $avatar === null) {
                        array_push($error, "You should fill all your profile informations !");
                        $status = Status::BAD_REQUEST;

                        return $response->withJson([
                            'error' => $error,
                            'alert' => $alert,
                            'data' => $data,
                        ])->withStatus($status);
                    }
                    $res = $userModel->get_user_like_link_count($pdo, $connected_user_id, $selected_user_id);
                    if ($res === false) {
                        array_push($error, "Something went wrong !");
                        $status = Status::BAD_REQUEST;

                        return $response->withJson([
                            'error' => $error,
                            'alert' => $alert,
                            'data' => $data,
                        ])->withStatus($status);
                    }
                    $like_link = $res;
                    if ((int) $like_link === 0) {
                        $res = $userModel->add_user_like_link($pdo, $connected_user_id, $selected_user_id);
                        $unblock_res = $userModel->delete_user_block_link($pdo, $connected_user_id, $selected_user_id);
                        $notification_res = true;
                        if ((int) $check_block_res === 0) {
                            $notification_res = $userModel->add_notification($pdo, $connected_user_id, $selected_user_id, "like");
                        }

                        $score_res = $scoreModel->update_score($pdo, $selected_user_id, 10);
                        if ($res === false || $notification_res === false || $score_res === false || $unblock_res === false) {
                            array_push($error, "Something went wrong !");
                            $status = Status::BAD_REQUEST;

                            return $response->withJson([
                                'error' => $error,
                                'alert' => $alert,
                                'data' => $data,
                            ])->withStatus($status);
                        }

                        array_push($alert, "Liked !");
                    } else {
                        $res = $userModel->delete_user_like_link($pdo, $connected_user_id, $selected_user_id);
                        $notification_res = true;
                        if ((int) $check_block_res === 0) {
                            $notification_res = $userModel->add_notification($pdo, $connected_user_id, $selected_user_id, "unlike");
                        }

                        $score_res = $scoreModel->update_score($pdo, $selected_user_id, -10);
                        if ($res === false || $notification_res === false || $score_res === false) {
                            array_push($error, "Something went wrong !");
                            $status = Status::BAD_REQUEST;

                            return $response->withJson([
                                'error' => $error,
                                'alert' => $alert,
                                'data' => $data,
                            ])->withStatus($status);
                        }
                        array_push($alert, "Unliked !");
                    }
                } else if ($action === "report") {
                    // Check if user has a completed profile
                    $bio = $connected_user[0][11];
                    $latitude = $connected_user[0][15];
                    $longitude = $connected_user[0][16];
                    $avatar = $connected_user[0][17];
                    if ($bio === null || $avatar === null || $longitude === null || $avatar === null) {
                        array_push($error, "You should fill all your profile informations !");
                        $status = Status::BAD_REQUEST;

                        return $response->withJson([
                            'error' => $error,
                            'alert' => $alert,
                            'data' => $data,
                        ])->withStatus($status);
                    }
                    $res = $userModel->get_user_report_link_count($pdo, $connected_user_id, $selected_user_id);
                    if ($res === false) {
                        array_push($error, "Something went wrong !");
                        $status = Status::BAD_REQUEST;

                        return $response->withJson([
                            'error' => $error,
                            'alert' => $alert,
                            'data' => $data,
                        ])->withStatus($status);
                    }
                    $report_link = $res;
                    if ((int) $report_link === 0) {
                        $res = $userModel->add_user_report_link($pdo, $connected_user_id, $selected_user_id);
                        $notification_res = true;
                        if ((int) $check_block_res === 0) {
                            $notification_res = $userModel->add_notification($pdo, $connected_user_id, $selected_user_id, "report");
                        }

                        $score_res = $scoreModel->update_score($pdo, $selected_user_id, -5);
                        if ($res === false || $notification_res === false || $score_res === false) {
                            array_push($error, "Something went wrong !");
                            $status = Status::BAD_REQUEST;

                            return $response->withJson([
                                'error' => $error,
                                'alert' => $alert,
                                'data' => $data,
                            ])->withStatus($status);
                        }
                        array_push($alert, "Reported !");
                    } else {
                        $res = $userModel->delete_user_report_link($pdo, $connected_user_id, $selected_user_id);
                        $score_res = $scoreModel->update_score($pdo, $selected_user_id, 5);
                        if ($res === false || $score_res === false) {
                            array_push($error, "Something went wrong !");
                            $status = Status::BAD_REQUEST;

                            return $response->withJson([
                                'error' => $error,
                                'alert' => $alert,
                                'data' => $data,
                            ])->withStatus($status);
                        }
                        array_push($alert, "Unreported !");
                    }
                } else if ($action === "block") {
                    // Check if user has a completed profile
                    $bio = $connected_user[0][11];
                    $latitude = $connected_user[0][15];
                    $longitude = $connected_user[0][16];
                    $avatar = $connected_user[0][17];
                    if ($bio === null || $avatar === null || $longitude === null || $avatar === null) {
                        array_push($error, "You should fill all your profile informations !");
                        $status = Status::BAD_REQUEST;

                        return $response->withJson([
                            'error' => $error,
                            'alert' => $alert,
                            'data' => $data,
                        ])->withStatus($status);
                    }
                    $res = $userModel->get_user_block_link_count($pdo, $connected_user_id, $selected_user_id);
                    if ($res === false) {
                        array_push($error, "Something went wrong !");
                        $status = Status::BAD_REQUEST;

                        return $response->withJson([
                            'error' => $error,
                            'alert' => $alert,
                            'data' => $data,
                        ])->withStatus($status);
                    }
                    $block_link = $res;
                    if ((int) $block_link === 0) {
                        $res = $userModel->add_user_block_link($pdo, $connected_user_id, $selected_user_id);
                        $unlike_res = $userModel->delete_user_like_link($pdo, $connected_user_id, $selected_user_id);
                        $notification_res = true;
                        if ((int) $check_block_res === 0) {
                            $notification_res = $userModel->add_notification($pdo, $connected_user_id, $selected_user_id, "block");
                        }

                        $score_res = $scoreModel->update_score($pdo, $selected_user_id, -5);
                        if ($res === false || $notification_res === false || $score_res === false || $unlike_res === false) {
                            array_push($error, "Something went wrong !");
                            $status = Status::BAD_REQUEST;

                            return $response->withJson([
                                'error' => $error,
                                'alert' => $alert,
                                'data' => $data,
                            ])->withStatus($status);
                        }
                        array_push($alert, "Blocked !");
                    } else {
                        $res = $userModel->delete_user_block_link($pdo, $connected_user_id, $selected_user_id);
                        $score_res = $scoreModel->update_score($pdo, $selected_user_id, 5);
                        if ($res === false || $score_res === false) {
                            array_push($error, "Something went wrong !");
                            $status = Status::BAD_REQUEST;

                            return $response->withJson([
                                'error' => $error,
                                'alert' => $alert,
                                'data' => $data,
                            ])->withStatus($status);
                        }
                        array_push($alert, "Unblocked !");
                    }
                } else {
                    array_push($error, "Input error !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                }
            }
        }
        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }
}
