<?php /* Smarty version 2.6.0, created on 2020-02-05 13:06:40
         compiled from ../../../modules/dashboard/dashlets/RssDashlet/templates/rss.tpl */ ?>
<style type="text/css">
.rss {
 background-color: #efece4;
 padding: 8px;
}

.rss ul {
	list-style: none;
	margin: 0;
	padding:0;
	border: 1px solid #d3d3d3;
	background-color: #fff;
	-moz-border-radius: 5px;
}

.rss ul li {
	padding: 4px;
	margin: 0;
	margin-bottom: 8px;
	border-bottom: 1px dotted #222;
}


.rss ul li span {
	padding: 0;
}

.rss ul li img {
	max-width: 64px;
	max-height: 64px;
	margin: 4px;
}

.rss ul li span p {
	font: normal 11px Arial;
	margin: 2px;
}

.rss ul li a {
	font: normal 12px Arial;
	color: #197583;
	text-decoration: none;
}

.rss ul li a:hover {
	text-decoration: underline;
}

</style>
<div class="rss">
<?php echo $this->_tpl_vars['rssFeed']; ?>

</div>