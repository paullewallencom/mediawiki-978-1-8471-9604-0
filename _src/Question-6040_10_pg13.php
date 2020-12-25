<div class="portlet" id="p-personal"> 
<h5><?php $this->msg('personaltools') ?></h5> 
<div class="pBody"> 
<ul> 
<?php foreach($this->data['personal_urls'] as $key => $item) { 
?><li id="pt-<?php echo htmlspecialchars($key) ?>"><a href="<?php 
echo htmlspecialchars($item['href']) ?>"<?php 
if(!empty($item['class'])) { ?> class="<?php 
echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php 
echo htmlspecialchars($item['text']) ?></a></li> 
<?php } ?> 
</ul> 
</div> 
</div> 
<div class="portlet" id="p-logo"> 
<a style="background-image: url(<?php $this->text('logopath') ?>);" <?php 
?> href="<?php echo htmlspecialchars($this-> data['nav_urls']['mainpage']['href'])?>" 
<?php title="<?php $this->msg('mainpage') ?>"></a> 
</div>

