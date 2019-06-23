//Extra functions
    function automat(){
      setInterval(function(){ $("#srvinfo").load("serverinfo.html #serverinfo") }, 1000);
      setInterval(function(){ scheduleProgress() }, 1000);
    }
    function scheduleProgress(){
      $("#srvprogress").load("serverinfo.html #serverprogress", function(){
        $("#progCPU").html(  drawprogress ($("#progCPU").attr("value") ) );
        $("#progMEM").html(  drawprogress( $("#progMEM").attr("value") ) );
        $("#progLOAD").html(  drawprogress( $("#progLOAD").attr("value") ) );
      });
    }
    function drawprogress(pval){
      var progress = "#";
      var pc = "</span>";
      var p10 = "<span style='color:green;'>";
      var p20 = "</span><span style='color:SeaGreen;'>";
      var p30 = "</span><span style='color:LimeGreen;'>";
      var p40 = "</span><span style='color:Lime;'>";
      var p50 = "</span><span style='color:GreenYellow;'>";
      var p60 = "</span><span style='color:Yellow;'>";
      var p70 = "</span><span style='color:Orange;'>";
      var p80 = "</span><span style='color:OrangeRed;'>";
      var p90 = "</span><span style='color:Firebrick;'>";
      var p100= "</span><span style='color:DarkRed;'>";

      var ival = (parseInt(pval));
      switch (true){
        case (ival<5):  progress = p10+"#"; break;
        case (ival<10): progress = p10+"##"; break;
        case (ival<15): progress = p10+"##"+p20+"#"; break;
        case (ival<20): progress = p10+"##"+p20+"##"; break;
        case (ival<25): progress = p10+"##"+p20+"##"+p30+"#"; break;
        case (ival<30): progress = p10+"##"+p20+"##"+p30+"##"; break;
        case (ival<35): progress = p10+"##"+p20+"##"+p30+"##"+p40+"#"; break;
        case (ival<40): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"; break;
        case (ival<45): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"#"; break;
        case (ival<50): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"; break;
        case (ival<55): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"#"; break;
        case (ival<60): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"; break;
        case (ival<65): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"#"; break;
        case (ival<70): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"##"; break;
        case (ival<75): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"##"+p80+"#"; break;
        case (ival<80): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"##"+p80+"##"; break;
        case (ival<85): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"##"+p80+"##"+p90+"#"; break;
        case (ival<90): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"##"+p80+"##"+p90+"##"; break;
        case (ival<95): progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"##"+p80+"##"+p90+"##"+p100+"#"; break;
        case (ival<100):progress = p10+"##"+p20+"##"+p30+"##"+p40+"##"+p50+"##"+p60+"##"+p70+"##"+p80+"##"+p90+"##"+p100+"##"; break;
      }
     return progress+pc;
    }

