<?php
session_start();

if (isset($_POST['zaloguj']))
    {
	$pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');

	$stmt = $pdo->prepare("SELECT * FROM uzytkownicy WHERE login = :login AND haslo = :haslo");
	$stmt->execute(['login' => $_POST['login'], 'haslo' => $_POST['haslo']]);
	$wynik = $stmt->fetch();
    if(!isset($_SESSION['bad_logon'])) {
        $_SESSION['bad_logon'] = 0;
    }
	if ($wynik)
    {
		$_SESSION['zalogowany'] = 'tak';
        $_SESSION['bad_logon'] = 0;
		$_SESSION['id'] = $wynik['id'];
		header("Location: index.php");
		exit();
	} else
    {
		$komunikat = "Zły login lub hasło.";
        $_SESSION['bad_logon']+=1;
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Logowanie</title>
        <meta charset="utf-8">
    </head>
    <body>
		<?php if(!empty($komunikat)): ?>
			<p style="font-weight: bold; color: red;"><?=$komunikat ?></p>
		<?php endif; ?>
		
        <form method="post" action="">
            <table>
                <tr>
                    <td>Login</td>
                    <td><input type="text" name="login" /></td>
                </tr>
                <tr>
                    <td>Hasło</td>
                    <td><input type="password" name="haslo" /></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="zaloguj" value="Zaloguj"
                    <?php
                        if(isset($_SESSION['bad_logon']) && $_SESSION['bad_logon'] > 2)
                        {
                            echo"disabled";
                        }
                            ?> /></td>
                </tr>
            </table>
        </form>
    </body>
</html>
