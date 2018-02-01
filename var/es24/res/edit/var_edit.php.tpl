{{* 文字コード自動判別用文字列 *}}
<link rel="stylesheet" media="screen" href="css/handsontable.full.css">
<script src="js/handsontable.full.js"></script>
<div align="left" style="white-space: nowrap;">
	<table class="main2">
		<tr class="trh">
			<th>変数名</th>
			<th>説明文、編集フォーム</th>
		</tr>
		{{foreach name=user_vars key=var_name item=var from=$user_vars}}
			<tr class="tr{{cycle values="1,2"}}">
				<td>
					{{$var_name}}
				</td>
				<td>
					<a name="{{$var_name}}"></a>
					{{$user_vars._comment.$var_name|escape:"html"}}<br>
					<span class="var_edit_link" data-target="{{$var_name}}"><a href="#{{$var_name}}">＋編集</a></span>
					<div style="display:none">
<input class="hot-load" type="button" name="" value="load" data-target="{{$var_name}}">
<input class="hot-save" type="button" name="" value="save" data-target="{{$var_name}}">
<div id="hot-{{$var_name}}"></div>

{{*
						{{if is_array($var)}}
							<textarea id="f1_var_{{$var_name}}" name="{{$var_name}}" cols="100" rows="10">{{foreach key=k item=v from=$var}}{{$k}}	{{$v}}
{{/foreach}}</textarea>
							<button type="button" name="button" onclick="return f1_submit('{{$var_name}}', 'f1_var_{{$var_name}}');">更新</button>
						{{else}}
							<input type="text" id="f1_var_{{$var_name}}" value="{{$var|escape:"html"}}">
							<button type="button" onclick="return f1_submit('{{$var_name}}', 'f1_var_{{$var_name}}');">更新</button>
						{{/if}}
*}}


					</div>
				</td>
			</tr>
		{{/foreach}}
	</table>
	
	<form method="POST" id="f1">
		<input type="hidden" name="f1_post" value="1">
		<input type="hidden" id="f1_var_name"  name="var_name"  value="">
		<input type="hidden" id="f1_var_value" name="var_value" value="">
	</form>
</div>
<script language="javascript">
<!--
	function f1_submit(var_name, var_id) {
		if (confirm('更新します、よろしいですか？')) {
			$("#f1_var_name").val(var_name);
			$("#f1_var_value").val($("#" + var_id).val());
			$("#f1").submit();
			return true;
		}
		else {
			return false;
		}
	}
	
	$(".var_edit_link").click(function() {
		$(this).next().show();
		$(this).hide();
		
		var get_params = {
			include_file: "plugin/{{$menukey}}/var_edit.php", 
			menukey: "{{$menukey}}", 
			ajax: 1, 
			var_name: $(this).attr("data-target"), 
		};
		$.get("inline.php", get_params, function(data){
			vars[data.var_name] = data.data;
			var e = document.getElementById("hot-" + data.var_name);
			hots[data.var_name] = new Handsontable(e, {
				  data: vars[data.var_name]
				, minSpareRows: 1
				, afterChange: function(changes, source){
					// changes : [[row, prop, oldVal, newVal], ...]
					
					// ロード直後は記録しない
					if (source === 'loadData') {return;}
					
					// 
				}
			});
		})
	});
	
	$(".hot-load").click(function(){
		var get_params = {
			include_file: "plugin/{{$menukey}}/var_edit.php", 
			menukey: "{{$menukey}}", 
			ajax: 1, 
			var_name: $(this).attr("data-target"), 
		};
		$.get("inline.php", get_params, function(data){
			vars[data.var_name] = data.data;
			hots[data.var_name].loadData(vars[data.var_name]);
		});
	});
	$(".hot-save").click(function(){
		var t = $(this).attr("data-target");
		var postdata = {
			var_name: t, 
			data: vars[t], 
		};
		$.post("inline.php?include_file=plugin/{{$menukey}}/var_edit.php&menukey={{$menukey}}&ajax=1", postdata, function(){
			
		});
	});
	
	var vars = {};
	var hots = {};
	
//-->
</script>
