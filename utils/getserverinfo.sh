#Gather Server info

dir=/var/www/arionum.tk/utils/
cd $dir

PREV_TOTAL=0
PREV_IDLE=0

while true; do

  ############## CPU INFO #############
  CPU=(`cat /proc/stat | grep '^cpu '`) # Get the total CPU statistics.
  unset CPU[0]                          # Discard the "cpu" prefix.
  IDLE=${CPU[4]}                        # Get the idle CPU time.

  # Calculate the total CPU time.
  TOTAL=0

  for VALUE in "${CPU[@]:0:4}"; do
    let "TOTAL=$TOTAL+$VALUE"
  done

  # Calculate the CPU usage since we last checked.
  let "DIFF_IDLE=$IDLE-$PREV_IDLE"
  let "DIFF_TOTAL=$TOTAL-$PREV_TOTAL"
  let "DIFF_USAGE=(1000*($DIFF_TOTAL-$DIFF_IDLE)/$DIFF_TOTAL+5)/10"
  CPUUSAGE=$(( DIFF_USAGE ));
  #echo -en "\rCPU: $DIFF_USAGE%  \b\b"

  # Remember the total and idle CPU times for the next check.
  PREV_TOTAL="$TOTAL"
  PREV_IDLE="$IDLE"

  ############## MEMORY INFO #############
  MEM=(`cat /proc/meminfo | grep Mem`) # Get the total MEM.
  unset MEM[0]                          # Discard the "mem" prefix.
  TotalMEM=${MEM[1]}                        # Get the MEM.
  FreeMEM=${MEM[4]}
  AvailMEM=${MEM[7]}
  UsedMEM=$(( (100 * ($TotalMEM - $FreeMEM) / $TotalMEM) ));

  LOAD=(`uptime`) # Get the serverLOAD.
  L1M=${LOAD[10]/,/}     # Get the Loads.
  L5M=${LOAD[11]/,/}
  L15M=${LOAD[12]/,/}
  L1P=$(echo "scale=2; (($L1M * 100)/4)" | bc -l);
  L5P=$(echo "scale=2; (($L5M * 100)/4)" | bc -l);
  L15P=$(echo "scale=2; (($L15M * 100)/4)" | bc -l);
  #echo -en "\r Load 1m: $L1M, Load 5m: $L5M, Load 15m: $L15M, Load 1 percent: $L1PERCENT \b\b"

  #Write to file
  echo "var srv_cpu=$CPUUSAGE;" > serverinfo.js
  echo "var srv_mem=$UsedMEM;" >> serverinfo.js
  echo "var srv_load=$L1P;" >> serverinfo.js
  echo "var srv_load1p=$L1P;" >> serverinfo.js
  echo "var srv_load5p=$L5P;" >> serverinfo.js
  echo "var srv_load15p=$L15P;" >> serverinfo.js
  echo "var srv_load1m=$L1M;" >> serverinfo.js
  echo "var srv_load5m=$L5M;" >> serverinfo.js
  echo "var srv_load15m=$L15M;" >> serverinfo.js

  #spread file
  cp serverinfo.js /var/www/mine-arionum.tk/utils
  cp serverinfo.js /var/www/smallminers-arionum.tk/utils
  cp serverinfo.js /var/www/bigminers-arionum.tk/utils
  cp serverinfo.js /var/www/solo-arionum.tk/utils
  cp serverinfo.js /var/www/pool-arionum.tk/utils

  #serverinfo.html
  echo "<div id='serverinfo'>" > serverinfo.html
#  echo "<script>" >> serverinfo.html
#  echo "  var srv_cpu=$CPUUSAGE;" >> serverinfo.html
#  echo "  var srv_mem=$UsedMEM;" >> serverinfo.html
#  echo "  var srv_load=$L1P;" >> serverinfo.html
#  echo "</script>" >> serverinfo.html
  echo " <span>Server Processor: <strong>$CPUUSAGE %</strong></span><br />" >> serverinfo.html
  echo " <span>Server Memory: <strong>$UsedMEM %</strong></span><br />" >> serverinfo.html
  echo " <span>Server Load: <strong>$L1P %</strong></span><br />" >> serverinfo.html
  echo "</div>" >> serverinfo.html
  echo "<div id='serverprogress'>" >> serverinfo.html
#  echo " <span><script>document.write( drawprogress($CPUUSAGE) )</script></span>" >> serverinfo.html
#  echo " <span><script>document.write( drawprogress($UsedMEM) )</script></span>" >> serverinfo.html
#  echo " <span><script>document.write( drawprogress($L1P) )</script></span>" >> serverinfo.html
  echo " <span id='progCPU' value='$CPUUSAGE'>#</span><br />" >> serverinfo.html
  echo " <span id='progMEM' value='$UsedMEM'>#</span><br />" >> serverinfo.html
  echo " <span id='progLOAD' value='$L1P'>#</span><br />" >> serverinfo.html
  echo "</div>" >> serverinfo.html

  #spread file
  cp serverinfo.html /var/www/arionum.tk/
  cp serverinfo.html /var/www/mine-arionum.tk/
  cp serverinfo.html /var/www/smallminers-arionum.tk/
  cp serverinfo.html /var/www/bigminers-arionum.tk/
  cp serverinfo.html /var/www/solo-arionum.tk/
  cp serverinfo.html /var/www/pool-arionum.tk/

  # Wait before checking again.
  sleep 5
done
