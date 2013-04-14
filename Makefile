it: clean
so: all

clean:
	rm -f ./TODO.txt

all: todo

todo:
	touch TODO.txt
	echo "# This file was generated automatically by grep-ing for 'TO DO' in the source code." > ./TODO.txt
	echo "# This file is meant as a pointer to the actual details in the files themselves." >> TODO.txt
	echo "# This file was created "`date` >> TODO.txt
	echo "" >> TODO.txt
	grep -n -r -e "TO DO" www >> TODO.txt
	grep -n -r -e "TO DO" bin >> TODO.txt

templates:

	php -q ./bin/compile-templates.php

secret:
	php -q ./bin/generate_secret.php

test:
	prove -v --exec 'php' ./tests/*.t

js:
	java -Xmx64m -jar lib/google-compiler/compiler-20100616.jar --js www/javascript/youarehere.js --js www/javascript/youarehere.assertion.js --js www/javascript/youarehere.map.js --js www/javascript/youarehere.woe.js --js www/javascript/youarehere.getlatlon.js > www/javascript/youarehere.min.js	
