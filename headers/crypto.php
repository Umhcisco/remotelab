<?php
function encrypt($string, $key) {
   $result = '';
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
   }
   return base64_encode($result);
}

function decrypt($string, $key) {
   $result = '';
   $string = base64_decode($string);
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)-ord($keychar));
      $result.=$char;
   }
   return $result;
}

$clave = "CiscO/2012/3.1416";
///http://www.aztlan-hack.org/index.php?command=1003&noticia=Encriptar-informacion-de-formularios-con-Javascript-y-PHP

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//These are two simple functions I built for 256-bit encryption/decryption with mcrypt.  I've decided to use MCRYPT_RIJNDAEL_128 because it's AES-compliant, and MCRYPT_MODE_CBC.  (ECB mode is inadequate for many purposes because it does not use an IV.)
//This function stores a hash of the data to verify that the data was decrypted successfully, but this could be easily removed if necessary.

function TOencrypt($decrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
 // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
 $key = hash('SHA256', $salt . $password, true);
 // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
 srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
 if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
 // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
 $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
 // We're done!
 return $iv_base64 . $encrypted;
 } 
 
 function TOdecrypt($encrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
 // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
 $key = hash('SHA256', $salt . $password, true);
 // Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
 $iv = base64_decode(substr($encrypted, 0, 22) . '==');
 // Remove $iv from $encrypted.
 $encrypted = substr($encrypted, 22);
 // Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
 $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
 // Retrieve $hash which is the last 32 characters of $decrypted.
 $hash = substr($decrypted, -32);
 // Remove the last 32 characters from $decrypted.
 $decrypted = substr($decrypted, 0, -32);
 // Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
 if (md5($decrypted) != $hash) return false;
 // Yay!
 return $decrypted;
 }
 
 ////////////
function randomToken($length) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = '';                           //password is a string
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length; $i++) {
        $n = mt_rand(0, $alphaLength);    
        $pass = $pass.$alphabet[$n];      //append a random character
    }
    return ($pass); 
}

 function truemod($num, $mod) {
   return ($mod + ($num % $mod)) % $mod;
 }
?>

<script type="text/javascript">
function Encriptar()
{
	var texto = document.getElementById("login").value;
	var semilla = "<?php $token=randomToken(10); echo $token; ?>";
	var suma = 0;
	var newtexto = "";
	 
	for(x=0; x < semilla.length; x++)
	{
		suma += semilla.charCodeAt(x);
	}
	 
	semilla = suma.toString();
	suma = 0;
	 
	for(z=0; z < semilla.length; z++)
	{
		suma += parseInt(semilla.charAt(z));
	}
	 
	semilla = parseInt(suma);
	 
	for(y=0; y < texto.length; y++)
	{
		if(texto.charCodeAt(y) + semilla > 126)
		{
			suma = ((texto.charCodeAt(y) + semilla) - 126) + 31;
		}
		else
		{
			suma = (texto.charCodeAt(y) + semilla);
		}
	 
		newtexto += String.fromCharCode(suma)
	 
	}
	 
	document.autentificacion.contrasena.value=newtexto;
	document.autentificacion.token.value="";
	 
	document.autentificacion.submit();
}


function isInt(value)
{
    var er = /^[0-9]+$/;

    return ( er.test(value) ) ? true : false;
}

function mod(a, n) {
    return a - (n * Math.floor(a/n));
}

function reverse(s){
    return s.split("").reverse().join("");
}

function reshuffle(temp,micaptcha,code){	
	code = typeof(code) != 'undefined' ? code : 1;
	var esEntero = isInt(code);
	//alert("code is a number? " + esEntero);		
	var tam = temp.length;
	//alert ("temp = " + temp + "\ntam = " + tam + "\ncode = " + code);
	if ((tam % 3) == 0){
		ultimo = temp.charAt(tam-1);
		temp = temp.substring(0,tam-1);
		temp += '+';
		temp += ultimo;
		tam=temp.length;
	}
	//alert("temp = " + temp + "\ntam = " + tam);		
	var tmp = '';		
	for (i=0; i<tam; i++){
		index = mod(-3*i-(-code),tam);
		//alert("i = " + i + "\ncode = " + code + "\n-3*i+code = " + (-3*i-(-code)) + "\n(i+code)%tam = " + mod(-3*i-(-code),tam) );
		swap = temp.charAt(index);
		if (swap=='@') swap = '*';
		tmp += swap;
		//alert("temp["+i+"] = " + temp.charAt(i) + "\nindex = " + index + "\nswap = " + swap + "\ntmp["+i+"] = " + tmp.charAt(i));
	}
	var flip = reverse(micaptcha);
	//alert("micaptcha = " + micaptcha + "\nflip = " + flip);
	var shuffle=flip+tmp;
	//alert("temp = " + temp + "\ntmp = " + tmp + "\nshuffle = " + shuffle);
	return shuffle;
}

</script>