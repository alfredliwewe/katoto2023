<?php
session_start();

require '../dbconnect.php';
require '../teacher/functions.php';
?>
<link rel="stylesheet" type="text/css" href="../w3css/w3.css">
<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
<script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../fontawesome/css/all.min.css">
<style type="text/css">
	.pointer:hover{
		cursor: pointer;
	}
</style>
<div class="w3-row w3-light-grey">
	<div class="w3-col m2">&nbsp;</div>
	<div class="w3-col m8">
<?php
if (isset($_SESSION['form'], $_SESSION['term'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND group = '$group' ");
	if ($sql->num_rows > 0) {
		$mega = [];
		while ($row = $sql->fetch_assoc()) {
			$studentId = $row['student'];
			$calculated = aggregate_points($studentId);
			$points = $calculated[0];
			$mega[$studentId] = $points;
		}

		asort($mega);


		$i = 1;
		foreach ($mega as $key => $val) {
    		$studentId = $key;
			$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
			$user_data = $user_sql->fetch_assoc();
			$student_name = $user_data['fullname'];

			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND group = '$group'");
			$total_count = $count_sql->num_rows;
			$calculated = aggregate_points($studentId);
			?>
			<div class="w3-padding-large w3-white w3-border" style="height: 900px;">
				<center>
					<img src="logo.png" width="120"><br>
					<h1>Our School System</h1>
					<h4>School Report</h4>
				</center>
				<br>
				<div class="w3-row">
					<div class="w3-half">
						<p>Student name: <b><?="$student_name";?></b></p>
						<p>Academic Year: <b><?php echo acayear($acayear);?></b></p>
						<p>Form: <b><?="$form";?></b></p>
					</div>
					<div class="w3-half">
						<p>Term: <b><?="$term";?></b></p>
						<p>Position: <b><?="$i";?></b></p>
					</div>
				</div><br>
				
			<table class="w3-table w3-table-all" border="1">
			<th>#</th><th>Subject Name</th><th>Score</th><th>Points</th><th>Remark</th>
			<?php
			$o = 1;
			while ($tq = $count_sql->fetch_assoc()) {
				echo "<tr><td>$o</td><td>".subject($tq['subject'])."</td><td>{$tq['score']}</td><td>".ma_points($tq['score'])."</td><td>You have passed</td></tr>";
				$o += 1;
			}
			echo "</table></div><br>";
			$i += 1;
		}
		
	}
	else{
		?>
		<div class="alert alert-danger">
			There is no student for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
		</div>
		<?php
	}
}
?>