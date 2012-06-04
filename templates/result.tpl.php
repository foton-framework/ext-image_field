<html>
<head>
<style type="text/css">
html,body,div,h1,h2,h3,h4,h5,h6,dt,dd,dl,p,blockquote,pre,form,fieldset,table,th,td{margin:0;padding:0}
img {border:0}
body {
	background:#AAA;
	padding:30px 0px 100px;
}
table {border-collapse:collapse}
td {
	font:11px Arial, sans-serif;
	color:#555;
	padding:5px;
	background:#EEE;
	vertical-align:middle;
	text-align:center;
	border:1px solid #AAA
}
td.selected {
	background:#09F;
	border-color:#039;
}
td.divider {
	background-color:#AAA;
	padding:3px;
}
.size {
	float:left;
	color:#595;
}
.ext {
	text-transform:uppercase;
	color:#955;
	float:right;
}
.nav {
	position: fixed;
	height:24px;
	padding:3px 0;
	width:100%;
	top:0;
	left:0;
	background:#FFF;
	border-bottom: 1px solid #AAA;
	box-shadow:0 0 5px rgba(0,0,0,.3);
	text-align:right;
}
.nav div {
	padding:0 15px 0 0;
}
.nav button {
	font:12px Arial !important;
}
.nav button {
	border:1px solid #aaa;
	background:#EEE;
	border-radius:10px;
	width:60px;
	text-align:center;
	display: inline-block;
}
.nav span {
	padding:0 15px;
	font:14px Arial, sans-serif;
}
</style>
</head>
<script type="text/javascript">
	var selected;
	var field = "<?=$field ?>";
	var prev_page = function(){
		window.parent.eif_current[field].page--;
		window.location = window.parent.eif_request_str(field);
	}
	var next_page = function(){
		window.parent.eif_current[field].page++;
		window.location = window.parent.eif_request_str(field);
	}
	var select = function(ob){
		if (selected) selected.className = 'thumb';

		if (selected == ob.parentNode)
		{
			selected = null;
			window.parent.select_img(field, '');
			return false;
		}

		selected = ob.parentNode;
		selected.className = 'selected';
		window.parent.select_img(field, ob.href);
		return false;
	}
</script>
<body class="<?=$type ?>">

<div class="nav">
	<div>
		<button onclick="prev_page()">&larr;</button>
		<span><script type="text/javascript">document.write(window.parent.eif_current[field].page+1)</script></span>
		<button onclick="next_page()">&rarr;</button>
	</div>
</div>

<table width="100%">
<? for ($i=0; $i<$columns; $i++): ?>
 <col width="<?=ceil(100/$columns)?>%" />
<? endfor ?>
<? foreach ($data as $i => $rows): ?>
	<? if ($i): ?>
		<tr><td class="divider" colspan="<?=$columns ?>"></td></tr>
	<? endif ?>
	<tr>
		<? foreach ($rows as $row): ?>
			<td class="thumb">
				<a href="<?=$row->url ?>" onclick="return select(this)"><img src="<?=$row->thumb ?>" alt="" /></a>
			</td>
		<? endforeach ?>
	</tr>
	<tr>
		<? foreach ($rows as $row): ?>
			<td class="info">
				<span class="size"><?=$row->size ?></span>
				<a href="<?=$row->url ?>" target="_blank">[OPEN]</a>
				<span class="ext"><?=$row->ext ?></span>
			</td>
		<? endforeach ?>
	</tr>
<? endforeach ?>
</table>
</body>
</html>