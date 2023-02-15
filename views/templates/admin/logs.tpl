{**
* Copyright Younited
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.md.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to tech@202-ecommerce.com so we can send you a copy immediately.
*
* @author 202 ecommerce <tech@202-ecommerce.com>
* @copyright Younited
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>Logs files
    </div>
    <div class="panel-body">
        <ul id="yp-fileslist">
            {foreach from=$logs_files item=logs_files_amonth  key=a_month}    
                <li>
                    <span class="caretyp">{$a_month|escape:'htmlall':'UTF-8'}</span>
                    <ul class="nestedyp">
                        {foreach from=$logs_files_amonth item=log_file}
                            <li><a href="{$logs_url|escape:'htmlall':'UTF-8'}&show_log_files&display_file={$a_month|escape:'htmlall':'UTF-8'}/{$log_file|escape:'htmlall':'UTF-8'}">
                                <p>{$log_file|escape:'htmlall':'UTF-8'}</p>
                            </a></li>
                        {/foreach}
                    </ul>
                </li>
            {/foreach}
        </ul>
    </div>
</div>

{if isset($logfile_content)}
<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>Content of file {$logfile_name|escape:'htmlall':'UTF-8'}
    </div>
    <div class="panel-body">
      <textarea style="resize: vertical;height:200px;">
        {$logfile_content|escape:'htmlall':'UTF-8'}
      </textarea>
    </div>
</div>
{/if}

<script type="text/javascript">
    var toggler = $(".caretyp").click(function() {
        this.parentElement.querySelector(".nestedyp").classList.toggle("activeyp");
        this.classList.toggle("caretyp-down");
    });
</script>

<style>
/* Remove margins and padding from the parent ul */
ul#yp-fileslist, #yp-fileslist ul {
    list-style-type: none!important;
}

#yp-fileslist {
  margin: 0;
  padding: 0;  
}

/* Style the caret/arrow */
.caretyp {
  cursor: pointer;
  user-select: none; /* Prevent text selection */
}

/* Create the caret/arrow with a unicode, and style it */
.caretyp::before {
  content: "\25B6";
  color: black;
  display: inline-block;
  margin-right: 6px;
}

/* Rotate the caret/arrow icon when clicked on (using JavaScript) */
.caretyp-down::before {
  transform: rotate(90deg);
}

/* Hide the nested list */
.nestedyp {
  display: none;
}

/* Show the nested list when the user clicks on the caret/arrow (with JavaScript) */
.activeyp {
  display: block;
}
</style>