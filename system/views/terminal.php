<!DOCTYPE html>

<title>gOPF Terminal v<?= \System\Core::VERSION ?></title>

<link rel="stylesheet" href="@/System/Terminal/style.css" />

<script src="@/System/Core/jQuery.js"></script>
<script src="@/System/Core/gOPF.js"></script>
<script src="@/gOPF/gPAE/gPAE.js"></script>
<script src="@/System/Terminal/Terminal.js"></script>

<div id="terminal">
	<div id="console" class="consoleFont"></div>
	<form>
		<table>
			<tr>
				<td>
					<span id="prompt" class="consoleFont"></span>
				</td>
				
				<td class="expand">
					<input type="text" id="command" class="consoleFont" autocomplete="off">
				</td>
			</tr>
		</table>
	</form>
</div>