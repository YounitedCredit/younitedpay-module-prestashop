<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>Logs files
    </div>
    <div class="panel-body">
        <ul id="yp-fileslist">
            {foreach from=$logs_files item=logs_files_amonth  key=a_month}    
                <li>
                    <span class="caretyp">{$a_month}</span>
                    <ul class="nestedyp">
                        {foreach from=$logs_files_amonth item=log_file}
                            <li><a target="_blank" href="{$logs_url}/{$a_month}/{$log_file}">
                                <p>{$log_file}</p>
                            </a></li>
                        {/foreach}
                    </ul>
                </li>
            {/foreach}
        </ul>
    </div>
</div>

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