<!DOCTYPE html>
<html>
  <head>
    <title>Ebay Scraper</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <script>
      var XMLHttpRequestObject = false;

      if (window.XMLHttpRequest)
      {
          XMLHttpRequestObject = new XMLHttpRequest();
      }
      else if (window.ActiveXObject)
      {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
  </script>
  </head>
  <body>
      <div class="container container-fluid">
        <div class="row" style="margin-top: 25px;">
            <div class="col-md-12 text-center">
              <h4>Import CSV File</h4>
            </div>
            <div class="col-md-12" style="margin-top: 25px;"></div>
            <form id="importFile" class="form-inline col-md-12">
              <div class="col-md-12">
                <input style="width: 100%;" type="text" class="form-control text-url" name="url" placeholder="published csv url">
              </div>
              <div class="col-md-12 progress-container" style="display: none; margin-top: 12px;">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0"></div>
                </div>
                <div style="text-align: center;">
                  <span class="current_index"></span> out of <span class="total_count"></span>
                </div>
                <div style="text-align: center; display: none;">
                  <span class="compiling-message">Compiling.....</span>
                </div>

              </div>
              <div class="col-md-12 text-center" style="margin-top: 12px;">
                  <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fa fa-upload"></i> IMPORT</button>
              </div>

            </form>
            <div class="col-md-12 text-center" style="margin-top: 12px;">
                <button class="btn btn-primary" id="downloadtBtn"><i class="fa fa-download"></i> DOWNLOAD</button>
            </div>

            <div class="col-md-12" style="margin-top: 12px;">
              <textarea rows="5" class="form-control ip-proxy"></textarea>
            </div>
        </div>
      </div>

      <script>
            $('#import-file-holder').on('change', function(){
                $fileName = $(this).val().split('\\');
                $('#upload-file-info').html($fileName.pop());
            });

            $('#downloadtBtn').click(function(){
              location.href = 'export.php';
            });

            $("form#importFile").submit(function(event){


            //disable the default form submission
            event.preventDefault();
            event.stopPropagation();

            var formData = new FormData($(this)[0]);
            $.ajax({
                url: 'action/extract.php',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response){

                    //$('#submitBtn').html('<i class="fa fa-upload"></i> IMPORT');

                  if(response === false){
                    alert('Please complete the input fields.');
                  }else{
                    $('#submitBtn').html('<i class="fa fa-spinner fa-spin"></i>');
                    $('.progress-container').css('display', 'inline');
                    $('.progress-bar').attr('aria-valuenow', 0);
                    $('.progress-bar').css('width', 0);
                    $('.current_index').text(0);
                    $('.total_count').text(JSON.parse(response).length);
                    exec(JSON.parse(response), 0);

                  }
                },
                error: function(xhr, textStatus, errorThrown){

                }
            });

            return false;

        });
      </script>
      <script>
        var urlSet = [];
        var lines = $('.ip-proxy').val().split(/\n/);
        var proxy = [];
        for (var i=0; i < lines.length; i++) {
          // only push this line if it contains a non whitespace character.
          if (/\S/.test(lines[i])) {
            proxy.push($.trim(lines[i]));
          }
        }

        console.log(proxy);
        function exec($urls, $i, proxy){
          $urlList = $urls;

          if($i < $urlList.length){
            curlTo($urlList[$i][0], proxy).done(function(response){
                urlSet.push(response);
                $progWidth = ($i + 1) * 100 / $urlList.length;
                $('.progress-bar').attr('aria-valuenow', ($i + 1));
                $('.progress-bar').css('width', $progWidth+'%');
                $('.current_index').text($i + 1);
                $i++;
                exec($urlList, $i);
            });
          }else{
            console.log(urlSet);
            $('.compiling-message').css('display', 'inline');
            // compile csv file
            compileCsv(urlSet).done(function(){

              setTimeout(function(){
                $('.compiling-message').css('display', 'none');
                $('.progress-container').css('display', 'none');
                //$('.text-url').value('');
                $('#submitBtn').html('<i class="fa fa-upload"></i> IMPORT');
                alert('Process Successfully Completed!');
              }, 1000);

            });
          }

        }
        function curlTo($url, $proxy){
          $dfd = $.Deferred();
            if(XMLHttpRequestObject)
            {

                XMLHttpRequestObject.open("POST", "action/exec.php");


                XMLHttpRequestObject.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

                XMLHttpRequestObject.onreadystatechange = function()
                {
                    if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
                    {
                        var response =  $.parseJSON(XMLHttpRequestObject.responseText);
                        $dfd.resolve(response);
                    }
                    if (XMLHttpRequestObject.status == 408 || XMLHttpRequestObject.status == 503 || XMLHttpRequestObject.status == 500){
                        $dfd.resolve(false);

                    }
                }
                XMLHttpRequestObject.send("param= "+ JSON.stringify({url: $url, proxy: $proxy}));


            }

            return $dfd.promise();
        }

        function compileCsv($url){
          $dfd = $.Deferred();
          if(XMLHttpRequestObject)
          {

              XMLHttpRequestObject.open("POST", "action/compile.php");


              XMLHttpRequestObject.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

              XMLHttpRequestObject.onreadystatechange = function()
              {
                  if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
                  {
                      var response =  $.parseJSON(XMLHttpRequestObject.responseText);
                        $dfd.resolve(response);

                  }
                  if (XMLHttpRequestObject.status == 408 || XMLHttpRequestObject.status == 503 || XMLHttpRequestObject.status == 500){
                        $dfd.resolve(false);
                  }
              }
              XMLHttpRequestObject.send("param= "+ JSON.stringify({url: $url}));


          }

          return $dfd.promise();
        }
      </script>
  </body>
</html>
