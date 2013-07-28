<h3><?php _e('Lock Login', 'okfn-utility') ?></h3>
<table class="form-table" id="menu">
	<tbody>
		<tr valign="top">
			<th scope="row">Login lock</th>
			<td>
				<label><input type="checkbox" <?php $checked = $okf_login_lock ? _e('checked="checked"') : ''; ?> value="1" name="okf_login_lock"> Enable login lock</label><br>
			</td>
		</tr>
	</tbody>
</table>