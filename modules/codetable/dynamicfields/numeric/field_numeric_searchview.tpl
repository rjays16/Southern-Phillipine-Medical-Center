<input filter="{$options.filter}" param="value" id="{$options.id}_value" name="{$options.name}[value]" class="{$className|default:"input"}" type="text" value="{$value|escape:"html"}" 
style="width:140px; text-align:right;"
onfocus="this.select();this.value=parseInt(this.value); if (isNaN(this.value)) this.value='';" 
onblur="this.value=parseInt(this.value); if (isNaN(this.value)) this.value='';" 
/>