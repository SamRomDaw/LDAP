<?php
    header("Content-Type: text/html; charser=UTF-8");

    // Credenciales de prueba
    $ldapuser = "xxxx";
    $ldappass = "xxxx";
    
    // Datos de acceso al servidor LDAP
    $server = "192.168.207.7";
    $port = "389";
    
    // Contexto donde se encuentran los usuarios
    $ldapdn = "ou=People,dc=daw2grup4,dc=com";
    
    // Atributos a recuperar
    $searchAttr = array("dn", "cn", "sn", "givenName");
    
    // Atributo para incorporar en la respuesta
    $displayAttr = "cn";
    
    // Respuesta por defecto
    $status = 1;
    $msg = "";
    $userDisplayName = "null";
    
    // Recuperar datos del POST
    if (isset($_POST['username'])) {
      $ldapuser = $_POST['username']; // Usuario LDAP
    }
    
    if (isset($_POST['password'])) {
      $ldappass = $_POST['password']; // Contraseña
    }
    
    // Establecer conexión con el servidor LDAP
    $ldapconn = ldap_connect("ldap://{$server}:{$port}")
      or die ("Can't connect with LDAP server.");
        
    // Autenticar contra el servidor LDAP
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

    if (@ldap_bind($ldapconn, "uid={$ldapuser},{$ldapdn}", $ldappass)) {
        // En caso de éxito, recuperar los datos del usuario
        $result = ldap_search($ldapconn, $ldapdn, "(uid={$ldapuser})", $searchAttr);
        $entries = ldap_get_entries($ldapconn, $result);
        
        if ($entries["count"]>0) {
          // Si hay resultados en la búsqueda
          $status = 0;
          if (isset($entries[0][$displayAttr])) {
            // Recuperar el atributo a incorporar en la respuesta
            $userDisplayName = $entries[0][$displayAttr][0];?>
            <html lang="es">
	      <head>
	        <meta charset="UTF-8">
		<link rel="stylesheet" href="assets/css/style.css" type="text/css" />
		<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" type="text/css" />
		<title>Bienvenido al Infierno >:)</title>
	      </head>
	      <body>
		<div class="alerta alert alert-info w-25 text-center">
		  <?php echo "Has iniciado sesión como {$userDisplayName}"; ?>
		</div>
		<div>
		  <a href="canviar_pass.php" class="btn btn-lg btn-warning active" role="button" aria-pressed="true">Cambiar la contraseña</a>
		  <a href="logout.php" class="btn btn-lg btn-outline-dark active" role="button" aria-pressed="true">Cerrar sessión</a>
		</div>

		<script type="text/javascript" src="lib/bootstrap/js/bootstrap.min.js"></script>
	      </body>
	    </html>
<?php
          } else {
            // Si el atributo no está definido para el usuario
            $userDisplayName = "-";
            $msg = "Atributo no disponible ({$displayAttr})";
          }
        } else {
          // Si no hay resultados en la búsqueda, mostrar un error
          $msg = "Error desconocido";
        }
    } else {
      // Si falla la autenticación, mostrar un error
?>
      <html lang="es">
	<head>
	  <meta charset="UTF-8">
	  <link rel="stylesheet" href="assets/css/style.css" type="text/css" />
	  <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" type="text/css" />
	</head>
	<body>
	  <script> alert('Usuario y/o contraseña inválidos.\n Vuelva a intentarlo porfavor.'); window.location.href='index.php'; </script>
	  <script type="text/javascript" src="lib/bootstrap/js/bootstrap.min.js"></script>
	</body>
      </html>
<?php
    }
    
    //Cerrar la conexión
    ldap_close($ldapconn);

    // Respuesta
    echo "$msg";
?>

