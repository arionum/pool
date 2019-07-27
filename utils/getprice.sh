#extract infos from arionum.info

dir=/var/www/arionum.tk/utils/
cd $dir
rm index.html*
wget arionum.info # index.html

#line numbers
priceline=$(grep -n 'Last Price' index.html | cut -f1 -d:)
priceline=$((priceline+1))

gpuline=$(grep -n 'GPU hashrate' index.html | cut -f1 -d:)
gpuline=$((gpuline+1))

cpuline=$(grep -n 'CPU hashrate' index.html | cut -f1 -d:)
cpuline=$((cpuline+2))


#extract price value
price=$(sed -n $priceline'p' index.html | sed 's/<h3>//' | sed 's/<[/]h3>//' | sed 's/^[[:blank:]]*//')

#extract GPU value
gpuhashrate=$(sed -n $gpuline'p' index.html | sed 's/<h3>//' | sed 's/<[/]h3>//' | sed 's/^[[:blank:]]*//' | sed 's/[[:blank:]]*$//' | sed 's/,/./' )

#extract CPU value
cpuhashrate=$(sed -n $cpuline'p' index.html | sed 's/<h3>//' | sed 's/<[/]h3>//' | sed 's/^[[:blank:]]*//' | sed 's/<h5>KH[/]s<[/]h5>//' | sed 's/[[:blank:]]*$//' | sed 's/,/./' )


#Write to file
echo "var aro_price=$price;" > price.js
echo "var aro_gpu_hashrate=$gpuhashrate;" >> price.js
echo "var aro_cpu_hashrate=$cpuhashrate;" >> price.js

#spread file
cp price.js /var/www/mine-arionum.tk/utils
cp price.js /var/www/smallminers-arionum.tk/utils
cp price.js /var/www/bigminers-arionum.tk/utils
cp price.js /var/www/solo-arionum.tk/utils
cp price.js /var/www/pool-arionum.tk/utils

