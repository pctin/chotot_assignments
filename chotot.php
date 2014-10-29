<!DOCTYPE HTML>
<html>
	<head>
		<style>
            div.ad_img_item {
                background: none repeat scroll 0 0 white;
                border: 1px solid #ddd;
                font-size: 9px !important;
                padding: 1px;
                text-align: center;
            }
            .extra_img-b {
                box-shadow: 0 0 3px rgba(150, 150, 150, 0.55), 1px 1px 0 white, 2px 2px 3px rgba(150, 150, 150, 0.55);
            }
            .ad-image-b, .extra_img-b {
                background: none repeat scroll 0 0 transparent;
                display: table-cell;
                height: 70px;
                min-width: 90px;
                vertical-align: middle;
                float: left;
                margin: 10px;
            }
		</style>
		<script>
            function swapAds(ad1, ad2) {
                var xmlhttp;
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                
                xmlhttp.open("POST", "chotot_ajax.php", true);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                xmlhttp.send("ad1=" + ad1 + "&ad2=" + ad2);
            }
            
			function allowDrop(ev) {
				ev.preventDefault();
			}

			function drag(ev) {
				ev.dataTransfer.setData("text/html", ev.target.id);
			}

			function drop(ev) {
				ev.preventDefault();
                // get data transfer
				var data = ev.dataTransfer.getData("text/html");
                var ad1 = data;
                var ad2 = ev.target.id;
                // get drag and drop element
				var dragDiv = document.getElementById(data).parentNode;
                var dragDivHtml = dragDiv.innerHTML;
                var dropDiv = ev.target.parentNode;
                var dropDivHtml = dropDiv.innerHTML;
                // swap content between 2 divs
                dropDiv.innerHTML = dragDivHtml;
				dragDiv.innerHTML = dropDivHtml;
                
                // call ajax
                swapAds(ad1, ad2);
			}
            
            function updateAds() {
                var xmlhttp;
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                
                xmlhttp.onreadystatechange=function() {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                        var newAds = JSON.parse(xmlhttp.responseText);
                        var newHtml = '';
                        for (var key in newAds) {
                            if (newAds.hasOwnProperty(key)) {
                                newHtml += '<div class="ad_img_item extra_img-b" ondrop="drop(event)" ondragover="allowDrop(event)"> ';
                                newHtml += '<img src="http://static.chotot.com.vn/listing_thumbs/' + newAds[key] + '/' + key + '.jpg" draggable="true" ondragstart="drag(event)" id="' + key + '">';
                                newHtml += '</div>';
                            }
                        }
                        // update new ads from chotot ho chi minh
                        if (newHtml != '') {
                            var root = document.getElementById('ads');
                            root.innerHTML = newHtml + root.innerHTML;
                        }
                    }
                }
                
                xmlhttp.open("GET", "chotot_ajax.php", true);
                xmlhttp.send();
            }
            
            function resetTimer() {
                clearInterval(myTimer);
                myTimer = setInterval(function () {updateAds()}, document.getElementById('select-time').value * 1000);
            }
            
            window.onload = function(){
                myTimer = setInterval(function () {updateAds()}, 5000);
            }
		</script>
	</head>
	<body>
        <div style="margin-left: 50px;">
            <label>Time Update</label>
            <select id="select-time" onchange="resetTimer();">
                <?php for($i = 5; $i<50; $i = $i + 5) { ?>
                    <option id="<?php echo $i ?>"><?php echo $i ?></option>
                <?php } ?>
            </select>
            <label>minutes</label>
        </div>
        <?php
        $data = json_decode(file_get_contents('chotot_data.json'), true);
        ?>
        <div id="ads" style="margin-left: 50px;">
            <?php foreach ($data as $key => $value) : ?>
                <div class="ad_img_item extra_img-b" ondrop="drop(event)" ondragover="allowDrop(event)">
                  <img src="http://static.chotot.com.vn/listing_thumbs/<?php echo $value . '/' . $key ?>.jpg" draggable="true" ondragstart="drag(event)" id="<?php echo $key ?>">
                </div>
            <?php endforeach; ?>
        </div>
	</body>
</html>
