<?php
session_start();
$form = "
		<form action='cashOut.php' method='GET'>
			Cash<input type='radio' name='method' value='cash'/>
			Credit<input type='radio' name='method' value='credit'/>
			Check<input type='radio' name='method' value='check'/>
			<input type='submit' name='' value='Cash Out'/>
		</form>
";
echo $form;
?>