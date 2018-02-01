{{* 文字コード自動判別用文字列 *}}
<div align="left" style="width:400;white-space: nowrap;">
	<form method="POST" id="f1">
		<input type="hidden" name="f1_post" value="1">
		<fieldset>
			<legend>{{$pstrres.f101}}</legend>
			<div align="center">
				<table class="main2">
					<tr class="trh">
						<th>{{$pstrres.f102}}</th>
						<th>{{$pstrres.f103}}</th>
						<th colspan="3">{{$pstrres.f104}}</th>
					</tr>
					<tr class="tr1">
						<td>{{$pstrres.f105}}</td>
						<td align="center">
							<input type="radio" name="stat_col" value="regist_date" {{if $stat_col == "regist_date"}}checked{{/if}}>
						</td>
						<td>
							<input type="text" name="regist_begin_date" value="{{$where_vals.regist_begin_date}}" class="date" size="10">
						</td>
						<td>
							　～　
						</td>
						<td>
							<input type="text" name="regist_end_date"   value="{{$where_vals.regist_end_date}}"   class="date" size="10">
						</td>
					</tr>
					<tr class="tr2">
						<td>{{$pstrres.f106}}</td>
						<td align="center">
							<input type="radio" name="stat_col" value="last_login_date" {{if $stat_col == "last_login_date"}}checked{{/if}}>
						</td>
						<td>
							<input type="text" name="login_begin_date" value="{{$where_vals.login_begin_date}}" class="date" size="10">
						</td>
						<td>
							　～　
						</td>
						<td>
							<input type="text" name="login_end_date"   value="{{$where_vals.login_end_date}}"   class="date" size="10">
						</td>
					</tr>
					<tr class="tr1">
						<td>{{$pstrres.f107}}</td>
						<td align="center">
							<input type="radio" name="stat_col" value="unregist_date" {{if $stat_col == "unregist_date"}}checked{{/if}}>
						</td>
						<td>
							<input type="text" name="unregist_begin_date" value="{{$where_vals.unregist_begin_date}}" class="date" size="10">
						</td>
						<td>
							　～　
						</td>
						<td>
							<input type="text" name="unregist_end_date"   value="{{$where_vals.unregist_end_date}}"   class="date" size="10">
{{*
<br>※退会していない場合の日付は 0000-00-00 になります。
*}}
						</td>
					</tr>
					<tr class="tr2">
						<td>{{$pstrres.f108}}</td>
						<td align="center">
							<input type="radio" name="stat_col" value="play_term" {{if $stat_col == "play_term"}}checked{{/if}}>
						</td>
						<td colspan="3">
							{{$pstrres.f109}}
						</td>
					</tr>
				</table>
				
				<button type="submit">{{$strres.b001}}</button>
			</div>
		</fieldset>
	</form>
	<form name="f3" method="POST" id="f3">
		<input type="hidden" name="f1_post" value=1>
		<input type="hidden" name="f2_post" value=1>
	</form>
</div>

{{if $is_f1_search}}

<div align="left" style="white-space: nowrap;">
	<form method="POST" id="f1">
		<fieldset>
			<legend>{{$pstrres.f201}}</legend>
			{{if $logs}}
				<div style="float:left">
					<table class="main2" style="min-width:600px">
						<tr class="trh">
							<th>　{{$pstrres.f202}}　</th>
							<th>　{{$pstrres.f203}}　</th>
							<th>　{{$pstrres.f204}}　</th>
							<th>{{$pstrres.f205}}</th>
						</tr>
						
						{{foreach name=logs item=i from=$logs}}
							<tr class="tr{{cycle values="1,2"}}">
								<td align="center">{{$i.date}}</td>
								<td align="right">{{$i.count}}</td>
								<td align="right">{{$i.ratio}} %</td>
								<td align="left">
									<img src="images/g/a.gif" width="{{$i.width|default:"0"}}" height="8" alt="{{$i.count|default:0}}">
								</td>
							</tr>
						{{/foreach}}
						
					</table>
				</div>
			{{else}}
				<p>{{$pstrres.f206}}</p>
			{{/if}}
		</fieldset>
	</form>
{{*
TODO: 
	表とグラフの縦幅が違いすぎるので上手く調整する
	<div style="float:left">
		<div id="chart" style="width:800;height:600;"></div>
	</div>
*}}
</div>



{{*
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.pointLabels.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.js"></script>
<script language="javascript" type="text/javascript">
<!--
$(document).ready(function(){
	
//	$("#tabs").tabs();
	
//	var src = [[2, 14, 6, 12, 31, 62, 36, 96, 18, 53, 43, 14, 19, 57, 53, 67, 51, 72, 39, 39, 43, 15, 88, 82, 15, 94, 98, 94, 59, 1, 33, 62, 100, 33, 37, 14, 85, 62, 77, 40, 86, 84, 29, 66]];
//	var tickers = ["2011-12-30", "2011-12-31", "2012-01-01", "2012-01-02", "2012-01-03", "2012-01-04", "2012-01-05", "2012-01-06", "2012-01-07", "2012-01-08", "2012-01-09", "2012-01-10", "2012-01-11", "2012-01-12", "2012-01-13", "2012-01-14", "2012-01-15", "2012-01-16", "2012-01-17", "2012-01-18", "2012-01-19", "2012-01-20", "2012-01-21", "2012-01-22", "2012-01-23", "2012-01-24", "2012-01-25", "2012-01-26", "2012-01-27", "2012-01-28", "2012-01-29", "2012-01-30", "2012-01-31", "2012-02-01", "2012-02-02", "2012-02-03", "2012-02-04", "2012-02-05", "2012-02-06", "2012-02-07", "2012-02-08", "2012-02-09", "2012-02-10", "2012-02-11"];
	var src = {{$graph_src}};
	var tickers = {{$graph_tickers}};
	var gs = {{$gs}};
	
	plot3 = $.jqplot('chart', src, {
		stackSeries: true,
		captureRightClick: true,
		seriesColors: ["#008DFF", "#00FF8D", "#800080", "#FF8D33"],
		seriesDefaults:{
			renderer:$.jqplot.BarRenderer,
			rendererOptions: {
				barDirection: 'horizontal', 
				barMargin: 10,
				highlightMouseDown: true
			},
			pointLabels: {
				show: false
			}
		},
		highlighter: {
			show: false     // TODO: ラベルに余計な情報とか入ってるので後で上手く調整する
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
			tickOptions: {
				angle: -30
			}
		},
		axes: {
			xaxis: {
				min: 0,
				showTicks: true,
				tickOptions: {
					mark: 'outside', 
					formatString:'%d'
				}, 
				padMin: 0
			},
			yaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: tickers, 
				tickOptions: {
					formatString: ''
				}
			}
		}
	});


});
//-->
</script>
*}}



{{/if}}

<pre style="border: 1px solid black;clear:both;">
{{$pstrres.note}}
{{*

使い方に関するノート

そもそもの使い方．
	集計対象：
		会員登録日なら 'その日に何人登録があったか' 
		最終ログイン日なら '最後に遊んだ日がその日のユーザは何人か' 
		退会日なら 'その日の退会者は何人か' 
		プレイ日数なら '何日間遊んだユーザは何人か' 
	
	検索条件：
		検索母体を絞り込む条件
		
	
	注意事項
		ラベルは歯抜けになることがある (2012-01-15 の次に 2012-01-17 になる、など)



１．会員状態にあるユーザの継続日数が知りたい (ラスプレの要望)
	・集計対象のチェックを '会員登録日' に設定
	・検索条件の退会日の終端値 (右側の枠) を '2012-01-01' (サービス開始前ならいつでも良い) に設定
	
	上記条件で現在会員となっているユーザの会員登録日が出される。


２．継続率が知りたい
	・集計対象のチェックを 'プレイ日数' に設定
	・(必要なら) 会員登録日を設定
	
	上記条件で何日間遊んだかの統計が取れる
	改善施策の効果測定など行う場合は登録日で施策前・施策後と絞って検索すると取れる。



note considering usage

how to use
add up target:
if membership registration date, "how many registrations were made in that day"
if last login date, "how many players played in that day"
if withdrawal date, "how many withdrawals were done in that day"
if number of playing days, "how many users played this much"
 
search condition:
condition that narrows down the search target
  
 
warnings
  labels might skip sometimes( like 2012-01-17comes after 2012-01-15)



1.want to know number of continuation days of users who have registered(request from Last Precious)
-set the check of add up target to "membership registration date"
set  end amount(right side) of withdrawal date of search condition to "2012-01-01(any time before service start)"
 
Show the membership registration date of the user that is still a member with condition written on top. 


2.want to know continuation ratio
-set add up target's check to ""playing days""
-(if need) set membership registration date
 
able to gain statistics how many days the user played by the condition on top
To measure effects of  improvement measure, you're able to gain by narrowing down the search with "before measure" and "after measure".

*}}
</pre>
