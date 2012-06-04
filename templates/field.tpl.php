<? $this->head_begin() ?>
<style type="text/css">
	.eif_container {background:#DDD; padding:5px; border-radius:5px; border:1px solid #AAA}
	.eif_container td {white-space:nowrap; padding:0 10px !important; vertical-align:middle;}
	.eif_container iframe {border:1px solid #CCC; background:#FFF; width:99%; height:220px; margin:0; padding:0;}
	.eif_container .eif_target_btn {display:block; float:left; background:#FFF; padding:5px 10px; border:1px solid #AAA; margin:0 0 0 10px; border-radius:3px}
	.eif_container .eif_file {float:left;  background:#FFF; width:250px !important;}
	.eif_container .eif_file input {border:inherit!important; width:200px !important;}
	.eif_container input.elf_title {background:#EEE; color:#AAA; border:1px dashed #AAA !important; width:100% !important; display:block; float:left; padding:5px;}
	.eif_container input.elf_title:focus {color:#000; background:#FFF; box-shadow:1px 1px 1px rgba(0,0,0,.2) inset; border:1px solid #AAA !important}
	.eif_container input.eif_error {border-color:#C00 !important; background:#FDD !important}
</style>
<script type="text/javascript">
	var eif_base_url       = "<?=$this->image_field->base_url ?>";
	var eif_targets        = <?=json_encode($this->image_field->targets) ?>;
	var eif_current        = {};
	var eif_request_str = function(f, t, q, p)
	{
		t = t || eif_current[f].target;
		q = q || eif_current[f].query;
		p = p || eif_current[f].page;
		return eif_base_url + t + '/?parse=1&field='+f+'&u=' + encodeURIComponent((eif_targets[t].query.replace('%s', q).replace('%d', p * eif_targets[t].page_iteration)));
	}
	var select_img = function(f, url)
	{
		$('#eif_' + f).find('input[type=hidden]').val(url);
		return false;
	}
</script>
<? $this->head_end() ?>

<? $box_id = 'eif_' . $fieldname; ?>

<div class="eif_container" id="<?=$box_id ?>">
	<table class="wrapper" width="100%">
	<col width="1%">
	<col width="" />
	<col width="1%">
	<tr>
		<td>
			<div class="eif_file">
				<?=h_form::file($fieldname) ?>
				<?=h_form::hidden('eif_' . $fieldname) ?>
			</div>
		</td>
		<td>
			<?=h_form::input('elf_title', $title, 'class="elf_title"') ?>
		</td>
		<td>
			<? foreach ($this->image_field->targets as $key => $opt): ?>
				<a class="eif_target_btn" href="#<?=$key ?>"><?=$opt['name'] ?></a>
			<? endforeach ?>
		</td>
	</tr>
	</table>

	<iframe if="xxxx" src="" frameborder="0" style="display:none"></iframe>

	<div style="clear:both"></div>
</div>

<script type="text/javascript">
(function(){
	var $box    = $("#<?=$box_id ?>");
	var $title  = $box.find('input[name=elf_title]');
	var $iframe = $box.find('iframe');
	var field   = "<?=$fieldname ?>";

	$box.find('a.eif_target_btn').click(function(){
		if ( ! $title.val())
		{
			$title.addClass('eif_error').focus();
			return false;
		}
		$(this).removeClass('eif_error');

		eif_current[field] = {
			page:   0,
			query:  $title.val(),
			target: this.href.split('#')[1]
		}

		$iframe.attr('src', eif_request_str(field)).slideDown();
		return false;
	});
	$("input[name=title]").live("keyup", function(){
		$title.val(this.value);
		// console.log($title);
	});
})();
</script>