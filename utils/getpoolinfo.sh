#extract infos from POOLs
while true; do

dir=/var/www/arionum.tk/utils/
cd $dir

echo "//Generated: getpoolinfo.sh" > poolinfo.js

######### POOLS ##########
AropoolBlock=$( curl -H 'user-agent: Mozilla/5.0' -s 'http://aropool.com:80/api.php?q=currentBlock' | jq -r '.current_block_height')
ArocoolBlock=$( curl -H 'user-agent: Mozilla/5.0' -s 'http://aro.cool:80/api.php?q=currentBlock' | jq -r '.current_block_height')
ArionumpoolBlock=$( curl -H 'user-agent: Mozilla/5.0' -s 'https://arionumpool.com/api.php?q=currentBlock' | jq -r '.current_block_height')
#ArionumtkBlock=$( curl -H 'user-agent: Mozilla/5.0' -s 'http://arionum.tk/api.php?q=currentBlock' | jq -r '.current_block_height')
ArionumtkBlock=$( curl -H 'user-agent: Mozilla/5.0' -s 'http://127.0.0.1:80/api.php?q=currentBlock' | jq -r '.current_block_height')

#Write to file
echo "var aropool_block=$AropoolBlock;" >> poolinfo.js
echo "var arocool_block=$ArocoolBlock;" >> poolinfo.js
echo "var arionumpool_block=$ArionumpoolBlock;" >> poolinfo.js
echo "var arionumtk_block=$ArionumtkBlock;" >> poolinfo.js

########## ARIONUM INFO ###########
rm index.html*
wget -O index.html --header 'user-agent: Mozilla/5.0' 'http://arionum.info/'

#line numbers
blockline=$(grep -n '>Current block<' index.html | cut -f1 -d:)
blockline=$((blockline+1))
ArionumInfo=$(sed -n $blockline'p' index.html | sed 's/<h3>//' | sed 's/<[/]h3>//' | sed 's/^[[:blank:]]*//')
#Write to file
echo "var arionuminfo_block=$ArionumInfo;" >> poolinfo.js

############## spread file ################
cp poolinfo.js /var/www/mine-arionum.tk/utils
cp poolinfo.js /var/www/smallminers-arionum.tk/utils
cp poolinfo.js /var/www/bigminers-arionum.tk/utils
cp poolinfo.js /var/www/solo-arionum.tk/utils
cp poolinfo.js /var/www/pool-arionum.tk/utils

#check Max block height - Force SANITY
needSanity="false"
if [ $ArionumtkBlock -lt $AropoolBlock ]
then
  needSanity="true"
  #echo "$AropoolBlock" > maxBlockHeight.txt
elif [ $ArionumtkBlock -lt $ArocoolBlock ]
then
  needSanity="true"
  #echo "$ArocoolBlock" > maxBlockHeight.txt
elif [ $ArionumtkBlock -lt $ArionumpoolBlock ]
then
  needSanity="true"
  #echo "$ArionumpoolBlock" > maxBlockHeight.txt
elif [ $ArionumtkBlock -lt $ArionumInfo ]
then
  needSanity="true"
  #echo "$ArionumInfo" > maxBlockHeight.txt
else
  needSanity="false"
  #rm maxBlockHeight.txt
  #rm /var/www/node-arionum.tk/maxBlockHeight.txt
  #echo "$ArionumtkBlock" > maxBlockHeight.txt
fi
#cp maxBlockHeight.txt /var/www/node-arionum.tk/  #indirect

#Call Sanity direct
if [ $needSanity = "true" ]
then
  #echo "$ArionumtkBlock" > callSanity.txt
  php /var/www/node-arionum.tk/sanity.php 
fi

#WAIT FOR NEXT RUN
sleep 10

done

