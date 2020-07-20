<?php

function doexecp($cmd, $text, $cwd, $env, $viewout) {
  $descriptorspec = array(
    0 => array("pipe", "r"),  // stdin.
    1 => array("pipe", "w"),  // stdout.
    2 => array("pipe", "w") );
  $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);
  if (is_resource($process)) {
    fwrite($pipes[0], $text . "\n");
    fclose($pipes[0]);
    if($viewout) {
      echo stream_get_contents($pipes[1]);
    }
    fclose($pipes[1]);
    fclose($pipes[2]);
    $return_value = proc_close($process);
  }
  return;
}

if(isset($_REQUEST["cmd"])) {
  header("Content-Type: text/plain;");
  switch($_REQUEST["cmd"]) {
  case "p":
    $text = $_REQUEST['containt'];
    echo "<pre>";
    doexecp("./p0", $text, $cwd, $env, true);
    doexecp("./p1", $text, $cwd, $env, true);
    echo "</pre>";
    break;
  case "k":
    echo "<pre>";
    doexecp("./konbu", "\n", $cwd, $env, true);
    echo "</pre>";
    break;
  case "g":
    // $convert = "/usr/local/bin/convert ";
    $convert = "convert ";
    $resize  = " -resize 300x300\> ";
    if($_REQUEST["mode"] == "m" || $_REQUEST["mode"] == "o") {
      echo "<textarea id='objfile'>";
      switch($_REQUEST["mode"]) {
      case "m":
        echo "newmtl material0\n";
        echo "Ka 1.000000 1.000000 1.000000\n";
        echo "Kd 1.000000 1.000000 1.000000\n";
        echo "Ks 1.000000 1.000000 1.000000\n";
        echo "map_Ka " . $_FILES['containt']['name'] . "\n";
        echo "map_Kd " . $_FILES['containt']['name'] . "\n\n";
        break;
      case "o":
        $temp = $_FILES['containt']['tmp_name'] . "-work.ppm";
        $tout = tempnam(dirname($_FILES['containt']['tmp_name']), 'obj');
        doexecp($convert . $_FILES['containt']['tmp_name'] . $resize . ' -compress none ' . $temp, '\n', $cwd, $env, false);
        doexecp('./goki obj 2 1 .25 0 ' . $temp . ' ' . $tout, '\n', $cwd, $env, false);
        echo file_get_contents($tout);
        unlink($temp);
        unlink($tout);
        unlink($tout . '.mtl');
        break;
      default:
        echo "Not implemented: " . $_REQUEST["mode"];
      }
      echo "</textarea><br/>";
      echo "<a href='#' onClick='javascript: document.getElementById(\"downobj\").href = window.URL.createObjectURL(new Blob([document.getElementById(\"objfile\").value, {type: \"text/plain\"}]));'>Make blob</a><br/>";
      echo "<a id='downobj'>Download</a>";
    } else {
      echo "<img src='data:image/png;base64,";
      $img  = file_get_contents($_FILES['containt']['tmp_name']);
      $temp = $_FILES['containt']['tmp_name'] . "-work.ppm";
      $tout = tempnam(dirname($_FILES['containt']['tmp_name']), 'obj');
      $tpng = $_FILES['containt']['tmp_name'] . "-work.png";
      doexecp($convert . $_FILES['containt']['tmp_name'] . $resize . ' -compress none ' . $temp, '\n', $cwd, $env, false);
      switch($_REQUEST["mode"]) {
      case "c":
        doexecp('./goki collect ' . $temp . ' ' . $tout . ' 1 1', '\n', $cwd, $env, false);
        break;
      case "b":
        doexecp('./goki bump ' . $temp . ' ' . $tout . ' 1 1', '\n', $cwd, $env, false);
        break;
      case "p":
        doexecp('./goki pextend ' . $temp . ' ' . $tout . ' 1 1', '\n', $cwd, $env, false);
        break;
      case "l":
        doexecp('./goki light ' . $temp . ' ' . $tout . ' 1 1', '\n', $cwd, $env, false);
        break;
      default:
        echo "Not implemented: " . $_REQUEST["mode"] . "' />";
        unlink($temp);
        exit(0);
        break;
      }
      doexecp($convert . ' ' . $tout . ' ' . $tpng, '\n', $cwd, $env, false);
      echo base64_encode(file_get_contents($tpng));
      echo "' />";
      unlink($temp);
      unlink($tout);
      unlink($tpng);
    }
    break;
  case "t":
    $dameji = array("!", '"', "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "\\", "]", "^", "_", "`", "{", "|", "}", "~", " ");
    $text   = $_REQUEST["containt"];
    $dtopic = $_REQUEST["topic"];
    $ddict0 = $_REQUEST["dict0"];
    $ddict1 = $_REQUEST["dict1"];
    $dname  = str_replace($dameji, "", $_REQUEST["dictname"]);
    // thanks to: https://stackoverflow.com/questions/2104653/trim-text-to-340-chars.
      $max_length = 1200;
      if(strlen($text) > $max_length) {
        $offset = ($max_length - 3) - strlen($text);
        $text = substr($text, 0, strrpos($text, ' ', $offset)) . '...';
      }
      $max_length = 200;
      if(strlen($dtopic) > $max_length) {
        $offset = ($max_length - 3) - strlen($dtopic);
        $dtopic = substr($dtopic, 0, strrpos($dtopic, ' ', $offset)) . '...';
      }
      if(strlen($ddict0) > $max_length) {
        $offset = ($max_length - 3) - strlen($ddict0);
        $ddict0 = substr($ddict0, 0, strrpos($ddict0, ' ', $offset)) . '...';
      }
      if(strlen($ddict1) > $max_length) {
        $offset = ($max_length - 3) - strlen($ddict1);
        $ddict1 = substr($ddict1, 0, strrpos($ddict1, ' ', $offset)) . '...';
      }
    $mode = "";
    switch($_REQUEST["mode"]) {
    case "s":
      $mode = "stat";
      break;
    case "r":
      $mode = "findroot";
      break;
    case "d":
      $mode = "toc";
      break;
    case "l":
      $mode = "lack";
      break;
    case "b":
      $mode = "lbalance";
      break;
    case "w":
      $mode = "lword";
      break;
    case "D":
      $mode = "diff";
      break;
    case "S":
      $mode = "same";
      break;
    default:
      break;
    }
    $tdir = tempnam('/tmp', 'puts');
    unlink($tdir);
    mkdir($tdir);
    mkdir($tdir . '/d0');
    mkdir($tdir . '/d1');
    $ttopic = $tdir . '/topic';
    $tdict0 = $tdir . '/d0/' . $dname;
    $tdict1 = $tdir . '/d1/' . $dname;
    file_put_contents($ttopic, $dtopic);
    file_put_contents($tdict0, $ddict0);
    file_put_contents($tdict1, $ddict1);
    switch($mode) {
    case "stat":
    case "findroot":
      if($dname != "" && filesize($tdict0) != 0) {
        error_log($tdict0);
        doexecp("./puts " . $mode . " words.txt " . $tdict0, $text, $cwd, $env, true);
      } else {
        doexecp("./puts " . $mode . " words.txt", $text, $cwd, $env, true);
      }
      break;
    case "lword":
    case "lbalance":
      echo "<pre>";
      doexecp("./puts " . $mode . " words.txt", $text, $cwd, $env, true);
      echo "</pre>";
      break;
    case "toc":
    case "lack";
      if(filesize($ttopic) == 0) {
        echo "No input";
      } else if($dname != "" && filesize($tdict0) != 0) {
        doexecp("./puts " . $mode . " words.txt " . $tdict0 . " -toc " . $ttopic, $text, $cwd, $env, true);
      } else {
        doexecp("./puts " . $mode . " words.txt -toc " . $ttopic, $text, $cwd, $env, true);
      }
      break;
    case "diff";
    case "same";
      if($dname != "" && filesize($tdict0) != 0 && filesize($tdict1)) {
        doexecp("./puts " . $mode . " words.txt -dict " . $tdict0 . " -dict2 " . $tdict1, $text, $cwd, $env, true);
      } else {
        echo "Empty dict.";
      }
      break;
    default:
      echo "Not implemented: " . $_REQUEST["mode"];
      break;
    }
    unlink($ttopic);
    unlink($tdict0);
    unlink($tdict1);
    rmdir($tdir . '/d0');
    rmdir($tdir . '/d1');
    rmdir($tdir);
    break;
  default:
    break;
  }
  exit(0);
}

header("Content-Type: text/html; charset=utf-8");
?>
<HTML>
<HEAD>
<TITLE>konbu.azurewebsites.net</TITLE>
<script language="javascript">
function toggle(elementid) {
  elem = document.getElementById(elementid);
  if(elem.style.display == "none")
    elem.style.display = "inline-block";
  else
    elem.style.display = "none";
  return;
}

// preset
function preset_select(set) {
  if(set == 'null') {
    document.getElementById("regex").value = "";
    document.getElementById("field").value = "";
    document.getElementById("type").value  = "";
    document.getElementById("sort").value  = "";
  } else if(set == 'apache_default_proxy') {
    document.getElementById("regex").value = unescape("%5E%28%5B0-9a-zA-Z%5C.%5D+%29%5B%5E%5C%5B%5D+%28%5C%5B.+%5C%5D%29%5B%5E%5C%22%5D+%5C%22%28GET%7CPOST%7CCONNECT%7CPROXY%7CHEAD%7COPTIONS%29%20%28%5B%5E%20%5D+%29%20.+%24");
    document.getElementById("field").value = unescape("From%2C%20Date%2C%20Method%2C%20URL");
    document.getElementById("type").value  = unescape("s%2C%20s%2C%20s%2C%20S%28/%3B%5C%3B3%29");
    document.getElementById("sort").value  = unescape("Method%2C%20URL");
  } else if(set == 'hostapd') {
    ;
  } else if(set == 'dhclient') {
    ;
  } else if(set == 'dhcpd') {
    ;
  } else if(set == 'pcap_tcpdump_option') {
    ;
  }
  return;
}

function asyncPost(cmd, sel, cont, outcont, fin) {
  var req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    var result = document.getElementById(outcont);
    if(req.readyState == 4) {
      if(req.status == 200) {
        result.innerHTML = req.responseText;
      } else {
        result.innerHTML = "Some error had be occur: " + req.status;
      }
    } else {
      result.innerHTML = "Connecting..." + req.responseText;
    }
  };
  fd = new FormData();
  fd.append("cmd", cmd);
  ssel = document.getElementById(sel);
  fd.append("mode", ssel.options[ssel.selectedIndex].value);
  if(fin == 0) {
    fd.append("containt", document.getElementById(cont).files[0]);
  } else if(fin == 1) {
    fd.append("containt", document.getElementById(cont).value);
  } else if(fin == 2) {
    fd.append("containt", document.getElementById(cont).value);
    fd.append("dictname", document.getElementById("puts_dname").value);
    fd.append("dict0", document.getElementById("puts_d0").value);
    fd.append("dict1", document.getElementById("puts_d1").value);
    fd.append("topic", document.getElementById("puts_topic").value);
  } else {
    alert("error in asyncPost");
    return;
  }
  req.open('POST', window.location.pathname, true);
  req.send(fd);
  return;
}
</script>
<link rel="stylesheet" type="text/css" href="./style.css" >
</HEAD>
<BODY>
Hello, this is konbu.azurewebsites.net the working sample page of the software located in <a href="https://github.com/bitsofcotton">github.com/bitsofcotton</a> . <br/><br/>
<ul>
<li>Image file: <input type="file" id="image_in" /><br />
  <select id="mode">
    <option value="c">collect</option>
    <option value="b">bump</option>
    <option value="p">extend</option>
    <option value="l">light</option>
    <option value="o">obj</option>
    <option value="m">mtl</option>
  </select><br/>
  <input type="button" onClick="javascript: asyncPost('g', 'mode', 'image_in', 'image_out', 0);" value="Calculate" /><br/>
  <p id="image_out"></p></li>
<li>Text file: <br/>
  <textarea id="puts_analyse" maxlength="1200" rows="30" cols="80"></textarea><br/>
  DictName:
  <textarea id="puts_dname" maxlength="20" rows="1" cols="20"></textarea><br/>
  Dict0:
  <textarea id="puts_d0" maxlength="400" rows="12" cols="20"></textarea><br/>
  Dict1:
  <textarea id="puts_d1" maxlength="400" rows="12" cols="20"></textarea><br/>
  Topic:
  <textarea id="puts_topic" maxlength="400" rows="12" cols="20"></textarea><br/>
  <select id="pmode">
    <option value="s">stat</option>
    <option value="r">root</option>
    <option value="b">balance</option>
    <option value="w">word</option>
    <option value="d">detail</option>
    <option value="l">lack</option>
    <option value="D">diff</option>
    <option value="S">same</option>
  </select>
  <input type="button" onClick="javascript: asyncPost('t', 'pmode', 'puts_analyse', 'puts_out', 2);" value="Analyse" /><br />
  <p id="puts_out"></p></li>
<li>Log file:
  <form method="POST" enctype="multipart/form-data" action="log.cgi">
    <label>Preset:
    <select id="preset" onchange="preset_select(document.getElementById('preset').value);">
      <option value="null" selected>Null</option>
      <option value="apache_default_proxy">Apache 1.3</option>
      <option value="hostapd">syslog (hostapd)</option>
      <option value="dhclient">syslog (dhclient)</option>
      <option value="dhcpd">syslog (dhcpd)</option>
      <option value="pcap_tcpdump_option">pcap (tcpdump -option)</option>
    </select>
    </label><br/><br/>
    <label>Regex to filter.<br/><input type="text" name="regex" id="regex" size="80"></label><br/>
    <label>Name of each field.<br/><input type="text" name="field" id="field" size="80" placeholder="[a-zA-Z0-9]+, comma separated."></label><br/>
    <label>Type of each field.<br/><input type="text" name="type" id="type" size="80" placeholder="'string' or 'url' or 'path', comma separated."></label><br/>
    <label>Fields to be shown.<br/><input type="text" name="sort" id="sort" size="80" placeholder="name of fields, comma separated."></label><br/>
    <label>Log file.<br/><input type="file" name="logfile" size="120"></label><br/>
    <label>Log threshold.<br/><input type="range" min="10" max="1000" name="threshold" value="10" required></label><br/>
    <label>Log node threshold.<br/><input type="range" min="1" max="60" name="thresholdnode" value="10" required></label><br/>
    <label><input type="submit" value="Graphize"></label>
  </form>
</li>
<li>Numerical data (float, \n separated.) : <br/>
  <textarea id="pred_in" maxlength="80000" rows="30" cols="80"></textarea><br/>
  <input type="button" onClick="javascript: asyncPost('p', 'mode', 'pred_in', 'pred_out', 1);" value="Calculate Result" /><br/>
  <p id="pred_out"></p></li>
<li>Feasible region: <input type="button" onClick="javascript: asyncPost('k', 'mode', 'konbu_out', 'konbu_out', 1);" value="Konbu Check Sample" /><br/>
  <p id="konbu_out"></p></li>
<li>
<div>
<pre>
<?php
system("ls -l *.php *.cgi goki puts konbu p0 p1");
echo "\n";
system("/bin/date");
echo "\n";
echo '<br/>HTTP_ACCEPT: ' . $_SERVER['HTTP_ACCEPT'] . '<br/>';
echo 'HTTP_ACCEPT_ENCODING: ' . $_SERVER['HTTP_ACCEPT_ENCODING'] . '<br/>';
echo 'HTTP_ACCEPT_LANGUAGE: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '<br/>';
echo 'HTTP_CONNECTION: ' . $_SERVER['HTTP_CONNECTION'] . '<br/>';
echo 'HTTP_HOST: ' . $_SERVER['HTTP_HOST'] . '<br/>';
echo 'HTTP_UPGRADE_INSECURE_REQUESTS: ' . $_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS'] . '<br/>';
echo 'HTTP_USER_AGENT: ' . $_SERVER['HTTP_USER_AGENT'] . '<br/>';
echo 'REMOTE_ADDR: ' . $_SERVER['REMOTE_ADDR'] . '<br/>';
echo 'REMOTE_PORT: ' . $_SERVER['REMOTE_PORT'] . '<br/>';
echo 'REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD'] . '<br/>';
echo 'REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . '<br/>';
echo 'SERVER_ADDR: ' . $_SERVER['SERVER_ADDR'] . '<br/>';
echo 'SERVER_PORT: ' . $_SERVER['SERVER_PORT'] . '<br/>';
echo 'SERVER_NAME: ' . $_SERVER['SERVER_NAME'] . '<br/>';
echo 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . '<br/>';
?>
</pre>
</div>
</li>
</ul>
</BODY>
</HTML>

