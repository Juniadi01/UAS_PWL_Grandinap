<?php
class Auth extends Controller
{
    public function login()
    {
        if (isLoggedIn()) redirect(isAdmin() ? 'dashboard' : 'account');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';

            $userModel = $this->model('User');
            $user = $userModel->findByEmail($email);

            // password_verify mencocokkan input dengan hash di DB
            if ($user && password_verify($pass, $user->password)) {
                $_SESSION['user'] = [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                    'photo' => $user->photo,
                ];
                setFlash('success', 'Selamat datang, ' . $user->name . '!');
                redirect($user->role === 'admin' ? 'dashboard' : 'account');
            }

            setFlash('danger', 'Email atau password salah.');
            $this->view('auth/login', ['title' => 'Login', 'email' => $email]);
            return;
        }

        $this->view('auth/login', ['title' => 'Login']);
    }

    public function register()
    {
        if (isLoggedIn()) redirect('account');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $pass  = $_POST['password'] ?? '';
            $pass2 = $_POST['password2'] ?? '';

            $errors = [];
            if (strlen($name) < 3)  $errors[] = 'Nama minimal 3 karakter.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
            if (strlen($pass) < 6)  $errors[] = 'Password minimal 6 karakter.';
            if ($pass !== $pass2)    $errors[] = 'Konfirmasi password tidak cocok.';

            $userModel = $this->model('User');
            if (!$errors && $userModel->findByEmail($email)) {
                $errors[] = 'Email sudah terdaftar.';
            }

            if ($errors) {
                $this->view('auth/register', [
                    'title' => 'Daftar', 'errors' => $errors,
                    'old' => compact('name', 'email', 'phone'),
                ]);
                return;
            }

            $userModel->create([
                'name' => $name, 'email' => $email,
                'password' => $pass, 'phone' => $phone, 'role' => 'customer',
            ]);
            setFlash('success', 'Pendaftaran berhasil. Silakan login.');
            redirect('auth/login');
        }

        $this->view('auth/register', ['title' => 'Daftar']);
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        redirect('auth/login');
    }
}
