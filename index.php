<?php
ini_set('display_errors', 'On');
require_once('class.gw.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Guild Wars 2 API Test</title>
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<link href="styles.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="wrapper">
			<h1>Guild Wars 2 API Test</h1>
			<?php
			$gw2			= new GW2($authkey = '');
			$acc_info	= $gw2->accountDetails();
			$chars		= $gw2->getCharacters();
			if(isset($chars['text']))
			{
				echo '<p>Error: ' . ucfirst($chars['text']) . '</p>';
			}
			else
			{
				// Do stuff
				?>
				<h3>Characters belonging to <i><?php echo $acc_info['name']; ?></i></h3>
				<?php
				echo '<ul>';
				foreach($chars as $char)
				{
					$details = $gw2->charInfo($char);
					?>
					<li>
						<?php
						echo $char;
						if(isset($details['text']))
							echo '<small>&ndash; ' . $details['text'] . '</small>';
						else
						{
							?>
							<br>
							<small>
								Level <?php echo $details['level'] . ' ' .$details['gender'] . ' ' . $details['race'] . ' ' . $details['profession']; ?>
								<br>Representing: <?php echo $details['guild_name'] . ' [' . $details['guild_tag'] . ']'; ?>
								<br>Created: <?php echo date('Y-m-d H:i', strtotime($details['created'])); ?>
								<br>Played for: <?php echo $details['name_me']['h'] . 'h ' . $details['name_me']['m'] . 'm ' . $details['name_me']['s'] . 's'; ?>
								<br>Deaths: <?php echo $details['deaths']; ?>
								<?php
								if(is_array($details['crafting']) and !empty($details['crafting']))
								{
									?>
									<br>Crafting disciplines:
									<?php
									foreach($details['crafting'] as $craft)
									{
										echo '<br><i class="fa fa-' .($craft['active'] == 1 ? 'check' : 'times').'-circle"></i> ' . $craft['discipline'] . ' (' . $craft['rating'] . ')';
									}
								}
								?>
							</small>
							
							<?php
						}
						?>
							
					</li>
					<?php
				}
				echo '</ul>';
				// echo '<pre>'; print_r($acc_info); echo '</pre>';
				// echo '<pre>'; print_r($gw2->charInfo('Sneffa')); echo '</pre>';
			}
			?>
		</div>
	</body>
</html>