<?php
$file_sol=fopen($filesol,"w+");
fwrite($file_sol,file_get_contents($filename_code));
fclose($file_sol);
$total=0;

$SOURCE="EVNT";
if( strpos( $_GET['Code_id'], $SOURCE ) !== false )
	{
	$temp=$myArray[0]."_Ranking";
	$Query="select count(user_id) from $temp where user_id=$roll";
	$res = mysqli_query($dbConn, $Query) or die(mysqli_error($dbConn));
	$r = mysqli_fetch_array($res);
		if($r[0]==0)
		{
		$Query1="insert into $temp(`user_id`) values('$roll')";
		mysqli_query($dbConn, $Query1) or die(mysqli_error($dbConn));
		} 
	$event_name=$myArray[0]."_Ranking";
	$query = "SELECT problem_input,problem_output,marks from test_cases where problem_id='$myArray[1]'";
	}
else
	{	
	$query = "SELECT problem_input,problem_output from test_cases where problem_id='$code_id'";
	}
$result = mysqli_query($dbConn, $query) or die(mysqli_error($dbConn));
while($row = mysqli_fetch_array($result))
       	{
	
	$filename_input=$row[0];
	$filename_output=$row[1];
  	$CC="gcc";
  	$fileoutput="output.txt";
	
	$mem_file_output=" > output.txt";
	$file_output=" > output.txt; } 2> time.txt";
	
	$out="{ /usr/bin/time -f %e ./a.out < ";
	$mem_out="valgrind --log-file='filename.txt' ./a.out < ";
	
	$filename_error="error.txt";
    	$command=$CC." -lm ".$filename_code;	
	$command_error=$command." 2>".$filename_error;
	$executable="./a.out";
	exec("chmod 777 $executable"); 
	exec("chmod 777 $filename_error");	
	shell_exec($command_error);
	$error=file_get_contents($filename_error);
	
	if(!strpos($error,"error"))
		{	
		
		if(trim($error)=="")
			{
		 	
			$out=$out.$filename_input.$file_output;
			shell_exec($out);
			$runtime=file_get_contents("time.txt");
			$mem_out=$mem_out.$filename_input.$mem_file_output;
			shell_exec($mem_out);
			$memory=shell_exec("grep -Po 'frees, \K.*(?= allocated)' filename.txt");
			
			$runtime=trim($runtime);
			}
	
		else		  
			{
			echo "<pre>$error</pre>";
			$out=$out.$filename_input.$file_output;
			shell_exec($out);
			$runtime=file_get_contents("time.txt");
			$mem_out=$mem_out.$filename_input.$mem_file_output;
			shell_exec($mem_out);
			$memory=shell_exec("grep -Po 'frees, \K.*(?= allocated)' filename.txt");
  			}

			
		$out="diff -q ".$filename_output." ".$fileoutput;
		$output=shell_exec($out);
		if(trim($output)=="")
			{
			$Result="Correct";
			$total=$total+$row[2];
			}
		else
			{
			$Result="Incorrect";
			}
			 


		}
	else
		{
		$Result="CompilationError";
		if( strpos( $_GET['Code_id'], $SOURCE ) !== false )
	{
$query1="INSERT into $sub(`submission_id`,`run_time`,`mem_usage`,`result`) values('$submission','$runtime','$memory','$Result')";
	}
	else
	{
$query1="INSERT into practice_submission(`submission_id`,`run_time`,`mem_usage`,`result`) values('$submission','$runtime','$memory','$Result')";
	}
	$result1 = mysqli_query($dbConn, $query1) or die(mysqli_error($dbConn));

		break;
		}



	if( strpos( $_GET['Code_id'], $SOURCE ) !== false )
	{
$query1="INSERT into $sub(`submission_id`,`run_time`,`mem_usage`,`result`) values('$submission','$runtime','$memory','$Result')";
	}
	else
	{
$query1="INSERT into practice_submission(`submission_id`,`run_time`,`mem_usage`,`result`) values('$submission','$runtime','$memory','$Result')";
	}
	$result1 = mysqli_query($dbConn, $query1) or die(mysqli_error($dbConn));
}

if( strpos( $_GET['Code_id'], $SOURCE ) !== false )
	{ 
$query2="INSERT INTO $Dir(`user_id`,`problem_id`,`id`,`date`,`result`,`time`,`runtime`,`memory`,`lang`,`address`) VALUES 	          ('$roll','$myArray[1]','$sum','$date','$Result','$time','$runtime','$memory','$languageID','$filesol')";

$query_add="UPDATE $temp set `$myArray[1]`=$total where user_id=$roll";
mysqli_query($dbConn, $query_add) or die(mysqli_error($dbConn));
	}
else
	{
$query2="INSERT INTO practice_solution(`user_id`,`problem_id`,`id`,`date`,`result`,`time`,`runtime`,`memory`,`lang`,`address`) VALUES ('$roll','$code_id','$submission','$date','$Result','$time','$runtime','$memory','$languageID','$filesol')";
	}
$result2 = mysqli_query($dbConn, $query2) or die(mysqli_error($dbConn));
exec("rm *.c");
exec("rm *.o");
exec("rm *.txt");
exec("rm $executable");
include("test_cases_output.php");
?>





