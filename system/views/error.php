<?php
	use System\View; 
	use System\I18n;
	use System\Error;
?>
<html>
	<head>
		<title>ooops!</title>
		<meta charset="UTF-8">
		
		<style>
			* {margin: 0; padding: 0;}
			body {background: #00a; color: #fff; font-family: courier; font-size: 17px; text-align: center; margin: 100px auto 0 auto; width: 1000px; line-height: 25px;}
			.negative {background:#fff; color:#00a; padding: 3px 8px;}
			.bold {font-weight: bold;}
			.rightSpace {margin-right: 30px;}
			.bottomSpace {margin-bottom: 60px;}
			.smaller {font-size: 13px}
			
			pre {margin-top: -2px; font-size: 12px !important; line-height: 15px; padding: -10px;}
			pre span.line {display: block;}
			pre span.highlight {font-weight: bold;}
			pre span.line span.number {color: #666 !important;}
		</style>
	</head>
	
	<body>
		<p class="bottomSpace bold">*** STOP: 0x676f7066 (0x00000067, 0x0000004F, 0x00000050, 0x0000000A)</p>
	
		<span class="negative bold">ERROR <?php View::sectionSlot('httpErrorCode'); ?></span>
		
		<p class="negative"><span class="bold rightSpace">*** <?php echo I18n::translate('AN_ERROR_OCCURED') ?> ***</span> <?php View::sectionSlot('errorMessage'); ?></p>
		<p class="bold smaller bottomSpace"><span><?php echo I18n::translate('WHILE_EXECUTING') ?></span> <?php View::sectionSlot('whileExecuting'); ?></p>
		
		<div style="text-align: left;">
			<?php
				if (Error::FILES) {
					foreach (array('content', 'exception') as $section) {
						?>
							<p class="negative bold smaller"><?php View::sectionSlot($section.'File')?></p>
							<pre class="negative smaller bottomSpace"><?php View::sectionSlot($section); ?></pre>
						<?php
					}
				}
			?>
		</div>
	</body>
</html>