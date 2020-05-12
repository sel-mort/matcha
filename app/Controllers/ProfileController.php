<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Crypt\Crypt;
use App\Models\LoginModel;
use App\Models\ProfileModel;
use App\Models\RegisterModel;
use App\Session\Session;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \DateTime;

class ProfileController extends Controller
{
    public function render_profile(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        Session::start();
        $username = Session::unserialize("user")["username"];
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
        if (!isset($res) || !$res) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
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
        $longitude = $res[0][16];
        $latitude = $res[0][15];
        $avatar = $res[0][17];

        // Interest
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
        $user_profile = array("username" => $username, "firstname" => $firstname, "lastname" => $lastname, "email" => $email, "age" => $age, "gender" => $gender, "bio" => $bio, "orientation" => $orientation, "score" => $score, "latitude" => $latitude, "longitude" => $longitude, "avatar" => $avatar, "interest" => $user_interests);
        $user_profile = json_encode($user_profile);

        return $this->render($response, "profile.twig", array("user_profile" => $user_profile));
    }

    public function update_profile(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        $username = $request->getParam('username');
        $firstname = $request->getParam('firstname');
        $lastname = $request->getParam('lastname');
        $password = $request->getParam('password');
        $email = $request->getParam('email');
        $age = $request->getParam('age');
        $bio = $request->getParam('bio');
        $interest = $request->getParam('interest');
        $female_gender = $request->getParam('female-gender');
        $male_gender = $request->getParam('male-gender');
        $female_orientation = $request->getParam('female-orientation');
        $male_orientation = $request->getParam('male-orientation');
        $both_orientation = $request->getParam('both-orientation');
        $longitude = $request->getParam('longitude');
        $latitude = $request->getParam('latitude');

        // Check params
        if (!Validator::check_array($username) || !Validator::check_array($firstname) || !Validator::check_array($email) || !Validator::check_array($lastname)
            || !Validator::check_array($password) || !Validator::check_array($age) || !Validator::check_array($bio) || !Validator::check_array($interest)
            || !Validator::check_array($female_gender) || !Validator::check_array($male_gender) || !Validator::check_array($female_orientation) || !Validator::check_array($male_orientation)
            || !Validator::check_array($both_orientation) || !Validator::check_array($longitude) || !Validator::check_array($latitude)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        $username = trim($username);
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        $password = trim($password);
        if (empty($password)) {
            Session::start();
            $password = Session::unserialize("user")["password"];
            // case (no password)
        }
        $email = strtolower(trim($email));
        $age = trim($age);
        $bio = trim($bio);
        $interest = trim($interest);
        $female_gender = trim($female_gender) === "true";
        $male_gender = trim($male_gender) === "true";
        $female_orientation = trim($female_orientation) === "true";
        $male_orientation = trim($male_orientation) === "true";
        $both_orientation = trim($both_orientation) === "true";
        $longitude = trim($longitude);
        $latitude = trim($latitude);

        if (!Validator::check_lenght($username, 5, 100)) {
            array_push($error, "Invalid Username lenght ! (>5)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_username($username)) {
            array_push($error, "Invalid Username ! (alphanumeric chars only)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_not_empty($firstname) || !Validator::check_lenght($firstname, 1, 100)) {
            array_push($error, "Invalid First name lenght !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_alpha($firstname)) {
            array_push($error, "Invalid First name ! (alpha characters only)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_not_empty($lastname) || !Validator::check_lenght($lastname, 1, 100)) {
            array_push($error, "Invalid Last name lenght !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_alpha($lastname)) {
            array_push($error, "Invalid Last name ! (alpha characters only)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_password($password)) {
            array_push($error, "Invalid Password !  (>5, 1 upper case, 1 lower case, 1 number, 1 special char)" . $password);
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_email($email)) {
            array_push($error, "Invalid Email !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_int($age) || !Validator::check_age($age)) {
            array_push($error, "Invalid Age !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_lenght($bio, 0, 10000)) {
            array_push($error, "Invalid Bio lenght ! (>5)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_lenght($interest, 0, 10000)) {
            array_push($error, "Invalid Interest lenght ! (>5)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        // Avatar upload
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $check_file = Validator::check_file($file);

            if ($check_file === 0) {
                array_push($error, "File extension error ! (jpg, jpeg, png)");
                $error_count++;
                $status = Status::BAD_REQUEST;
            } else if ($check_file === 2) {
                array_push($error, "File type error !");
                $error_count++;
                $status = Status::BAD_REQUEST;
            } else if ($check_file === 3) {
                array_push($error, "File size error ! (5 Ko - 5 Mo)");
                $error_count++;
                $status = Status::BAD_REQUEST;
            } else if ($check_file === 4) {
                array_push($error, "File error !");
                $error_count++;
                $status = Status::BAD_REQUEST;
            } else {
                $upload_dir = "images/avatars/";
                $filename = $file['name'];
                $filetmpname = $file['tmp_name'];
                $filesize = $file['size'];
                $fileerror = $file['error'];
                $filetype = $file['type'];
                $ext = explode('.', $filename);
                $fileextension = strtolower(end($ext));
                $allowedextension = array('jpg', 'jpeg', 'png');
                $uniqname = uniqid();
                $newfilename = $uniqname . ".png";
                $filedestination = $upload_dir . $newfilename;
                @imagepng(@imagecreatefromstring(file_get_contents($filetmpname)), $filedestination);
                unlink($filetmpname);
                Session::start();
                $connected_username = Session::unserialize("user")["username"];
                $profileModel = new ProfileModel();
                $res = $profileModel->update_profile_avatar($pdo, $connected_username, $uniqname); // ? ? ? res check
                if ($res === false) {
                    array_push($error, "Avatar error !");
                }
            }
        }

        $gender = -1;
        if ($female_gender === false && $male_gender === true) {
            $gender = 1;
        } else if ($female_gender === true && $male_gender === false) {
            $gender = 0;
        } else {
            array_push($error, "Gender error !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }
        $orientation = -1;
        if ($female_orientation === false && $both_orientation === false && $male_orientation === true) {
            $orientation = 1;
        } else if ($female_orientation === true && $both_orientation === false && $male_orientation === false) {
            $orientation = 0;
        } else if ($female_orientation === false && $both_orientation === true && $male_orientation === false) {
            $orientation = 2;
        } else {
            array_push($error, "Orientation error !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        // Position update
        if (isset($longitude) && isset($latitude)) {
            if (!empty($longitude) && !empty($latitude)) {
                if (!Validator::check_latitude($latitude) || !Validator::check_longitude($longitude)) {
                    array_push($error, "Invalid coordinates !)");
                    $error_count++;
                    $status = Status::BAD_REQUEST;
                } else {
                    Session::start();
                    $connected_username = Session::unserialize("user")["username"];
                    $loginModel = new LoginModel();
                    $res = $loginModel->update_position($pdo, $connected_username, $latitude, $longitude);
                    if ($res === false) {
                        array_push($error, "Position error !");
                    }
                }
            }
        }

        if ($error_count === 0) {
            $registerModel = new RegisterModel();
            $profileModel = new ProfileModel();
            $username_count = $registerModel->username_count($pdo, $username);
            $email_count = $registerModel->email_count($pdo, $email);
            if ($username_count === false || $email_count === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }

            Session::start();
            if ($username_count === 0 || ($username === Session::unserialize("user")["username"])) {
                Session::start();
                $connected_username = Session::unserialize("user")["username"];
                // Email management
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
                $user_email = $res[0][3];

                if ($email_count !== 0 && $email !== $user_email) {
                    array_push($error, "Email already used !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                }
                // Age to birthdate
                $now = date("Y/m/d");
                $now = strtotime("-" . $age . " year", time());
                $birthdate = date("Y-m-d", $now);

                // Interest update
                $interest = explode("#", $interest);

                if (count($interest) > 1) {
                    for ($i = 1; $i < count($interest); $i++) {
                        if (!Validator::check_lenght($interest[$i], 1, 100)) {
                            array_push($error, "Interest tag lenght !");
                            $status = Status::BAD_REQUEST;

                            return $response->withJson([
                                'error' => $error,
                                'alert' => $alert,
                                'data' => $data,
                            ])->withStatus($status);
                        }
                    }
                    for ($i = 1; $i < count($interest); $i++) {
                        if (!empty(trim($interest[$i]))) {
                            $res = $profileModel->add_interest($pdo, strtolower(trim($interest[$i])));
                            if ($res === false) {
                                array_push($error, "Something went wrong !");
                                $status = Status::BAD_REQUEST;

                                return $response->withJson([
                                    'error' => $error,
                                    'alert' => $alert,
                                    'data' => $data,
                                ])->withStatus($status);
                            }
                        }
                    }
                }

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

                $res = $profileModel->delete_user_interest($pdo, $user_id);
                if ($res === false) {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;

                    return $response->withJson([
                        'error' => $error,
                        'alert' => $alert,
                        'data' => $data,
                    ])->withStatus($status);
                }

                if (count($interest) > 1) {
                    for ($i = 1; $i < count($interest); $i++) {
                        if (!empty(trim($interest[$i]))) {
                            // Get the id of every interest
                            $res = $profileModel->get_interest_id($pdo, strtolower(trim($interest[$i])));
                            if ($res === false) {
                                array_push($error, "Something went wrong !");
                                $status = Status::BAD_REQUEST;

                                return $response->withJson([
                                    'error' => $error,
                                    'alert' => $alert,
                                    'data' => $data,
                                ])->withStatus($status);
                            }
                            $interest_id = $res[0][0];
                            $res = $profileModel->add_user_interest($pdo, $user_id, $interest_id);
                            if ($res === false) {
                                array_push($error, "Something went wrong !");
                                $status = Status::BAD_REQUEST;

                                return $response->withJson([
                                    'error' => $error,
                                    'alert' => $alert,
                                    'data' => $data,
                                ])->withStatus($status);
                            }
                        }
                    }
                }
                $update_profile = $profileModel->update_profile($pdo, $username, $firstname, $lastname, Crypt::crypt_str($password), $email, $birthdate, $gender, $orientation, $bio, $connected_username);
                if ($update_profile === true) {
                    array_push($alert, "Update successful !");
                    // Session update
                    Session::start();
                    Session::serialize("user", array("username" => $username, "password" => $password));
                    Status::CREATED;
                } else {
                    array_push($error, "Something gone wrong !");
                    Status::BAD_REQUEST;
                }
            } else if ($username_count === 1) {
                array_push($error, "Username already used !");
                Status::BAD_REQUEST;
            } else {
                array_push($error, "Something gone wrong !");
                Status::BAD_REQUEST;
            }
        }

        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }
}
