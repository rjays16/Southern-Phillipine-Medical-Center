<?php 
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}classes/json/json.php";
?>

<link rel="stylesheet" href="<?php echo $root_path.'/css/flot/examples.css'; ?>" type="text/css" media="all" />

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $root_path.'/js/flot/jquery.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo $root_path.'/js/flot/jquery.flot.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo $root_path.'/js/flot/jquery.flot.pie.js'; ?>" ></script>

<style type="text/css">

  .demo-container {
    position: relative;
    height: 500px;
  }

  #placeholder {
    width: 650px;
  }

  </style>

<script type="text/javascript">
  
  var $J = jQuery.noConflict();

  function display(data){
    var placeholder = $J("#placeholder");
    placeholder.unbind();

    $J.plot(placeholder, data, {
      series: {
        pie: { 
          show: true,
          radius: 1,
          /*label: {
            show: true,
            radius: 1,
            formatter: labelFormatter,
            background: { 
              opacity: 0.5,
              color: "#000"
            }
          }*/
          label: {
              show: true,
              radius: 0.5,
              formatter: labelFormatter,
              /*background: {
                opacity: 0.8
              }*/
            }
        }
      },
      legend: {
        show: true
      },
      grid: {
            hoverable: true
        }
    });
  }

  // A custom label formatter used by several of the plots

  function labelFormatter(label, series) {
    return "<div style='font-size:8pt; text-align:center; padding:2px; color:black;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
  }

  function __startLoadingbar() {
    element = $('#loading_indicator');
    $(element).css({ visibility:'visible' });
  }

  function __stopLoadingbar() {
    element = $('#loading_indicator');
    $(element).css({ visibility:'hidden' });
  }

  function refreshChart() {;
    __startLoadingbar();

    var field = $.trim($('#type_of_person').val());
    var year = '<?php echo $report_year; ?>';

    $J.ajax({
      type: "POST",
      url: '../reports/ajax/ajax_consulted_patient.php',
      dataType: 'json',
      data: {
        'field':field,
        'year':year,
      },
      error: function(request, error) {
              if (error == "timeout") {
                alert("The request timed out, please resubmit");
              }
              else {
                alert("ERROR: " + error);
              }
          },
      success: function(  data ) {
              //console.log(data);
              display(data);
              __stopLoadingbar();
          }
    });
  }
  </script>
  <div id="header">
    <h2>Admitted/Consulted Patients</h2>
    <table style="margin-top: 20px;">
      <tr>
        <td valign="center">Type of Person: </td>
        <td valign="center" style="padding-top:2px; margin-left:10px;">
          <select id="type_of_person" onchange="refreshChart()">
            <option value="all">ALL</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </td>
      </tr>
    </table>
    </span>
  </div>

  <div>
  <div id="loading_indicator" class="ajax-loading-bar" style="visibility:hidden"></div>
  
  </div>

  <div id="content">

    <div class="demo-container">
      <div id="placeholder" class="demo-placeholder"></div>
    </div>
  </div>

<script type="text/javascript">
  
  $J(function() {
    $J("#type_of_person").change();
  });

</script>

<?php die(""); ?>