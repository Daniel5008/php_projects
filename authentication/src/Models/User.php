<?php

namespace Src\Models;

use Exception;
use Src\Database\SqlConnection;
use Src\Models\Model;

class User extends Model
{

   public function register()
   {

      $sql = new SqlConnection();

      $sql->select("CALL sp_register_user(:username, :email, :password)", [
         ":username" => $this->getusername(),
         ":email" => $this->getemail(),
         ":password" => User::getPasswordHash($this->getpassword())
      ]);
   }

   public static function getPasswordHash($password)
   {
      return password_hash($password, PASSWORD_DEFAULT, [
         'cost' => 12
      ]);
   }

   public static function checkUsernameInUse($username)
   {
      $sql = new SqlConnection();

      $result = $sql->select("SELECT id from tb_users WHERE username = :username", [
         ":username" => $username
      ]);

      return count($result) > 0;
   }

   public static function checkEmailInUse($email)
   {
      $sql = new SqlConnection();

      $result = $sql->select("SELECT id from tb_users WHERE email = :email", [
         ":email" => $email
      ]);

      return count($result) > 0;
   }

   public static function login($usernameOrEmail, $password, $ipAddress)
   {
      $sql = new SqlConnection();

      $result = $sql->select('SELECT id, username, password FROM tb_users WHERE username = :username OR email = :email', [
         ':username' => $usernameOrEmail,
         ':email' => $usernameOrEmail
      ]);

      if (User::hasExceededLoginAttempts($usernameOrEmail, $ipAddress)) {
         $_SESSION['loginError'] = 'Muitas tentativas de login. Tente novamente mais tarde.';
         return;
      }

      if (count($result) == 0) {
         User::registerLoginAttempt($usernameOrEmail, $ipAddress);
         $_SESSION['loginError'] = 'Nenhuma conta encontrada para as credenciais informadas.';
         return;
      }

      $data = $result[0];

      if (password_verify($password, $data['password'])) {

         User::clearLoginAttempts($usernameOrEmail, $ipAddress);

         unset($data['password']);

         $user = new User();
         $user->setData($data);
         $_SESSION["user"] = $user->getValues();
         $_SESSION["loginError"] = null;
         return;

      } else {
         User::registerLoginAttempt($usernameOrEmail, $ipAddress);
         $_SESSION['loginError'] = 'Credenciais inválidas.';
         return;
      }

   }

   public static function registerLoginAttempt($usernameOrEmail, $ip)
   {
      $sql = new SqlConnection();

      $sql->query('INSERT INTO tb_login_attempts (username_or_email, ip_address) VALUES (:username, :ip)', [
         ':username' => $usernameOrEmail,
         ':ip' => $ip
      ]);
   }

   public static function hasExceededLoginAttempts($usernameOrEmail, $ip)
   {
      $sql = new SqlConnection();

      $result = $sql->select('SELECT COUNT(*) AS total_attempts
                              FROM tb_login_attempts
                              WHERE (username_or_email = :username OR ip_address = :ip)
                              AND attemp_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)',
         [
            ':username' => $usernameOrEmail,
            ':ip' => $ip
         ]
      );

      $total_attempts = $result[0]['total_attempts'];

      return $total_attempts >= 5;
   }

   public static function clearLoginAttempts($usernameOrEmail, $ip)
   {
      $sql = new SqlConnection();

      $sql->query('DELETE FROM tb_login_attempts WHERE username_or_email = :username OR ip_address = :ip', [
         ':username' => $usernameOrEmail,
         ':ip' => $ip
      ]);
   }

   public static function isUserLoggedIn()
   {
      if (!isset($_SESSION['user']) || !$_SESSION['user'] || !(intval($_SESSION['user']['id']) > 0)) {
         return false;
      }

      return true;
   }

   public static function getFromSession()
   {
      $user = new User();

      if (isset($_SESSION['user']) && intval($_SESSION['user']['id']) > 0) {

         $user->setData($_SESSION['user']);

      }

      return $user;
   }
}