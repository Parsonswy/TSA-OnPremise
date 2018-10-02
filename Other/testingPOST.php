<?php
echo "
	<form action='./testingPOST.php' method='POST'>
		<input type='texting' name='txt' value=''/>
		<select name='text'>
			<option value='1'>1</option>
			<option value='2'>2</option>
		</select>
		<input type='submit' name='go' value='go'/>
	</form> 
";
echo $_POST['txt'];
echo $_POST['text'];
?>