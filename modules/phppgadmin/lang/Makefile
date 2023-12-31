# This Makefile recodes source lang files into HTML Unicode.
# You should add your encodings to this file.  You need to have GNU Recode
# installed.
# 
# It is important to fix the first and last lines of the file <?php and ?>
# using 'awk'
#
# Modifications by Dave Smith, 2003-11-10:
#	Added TARGETS variable for easy listing of all lang files.
#	Added 'prepare' target to check that 'recode' is installed.
#	Added 'clean' target to nuke recoded files. 
#	Surpressed verbose command line dumpage with '@'.
#	Added dependency checking for incremental recoding.
#	Added pretty "Recoding ..." messages.
#	Added 'chmod 644' to 'all' target for correct file permissions.
# 
# $Id: Makefile,v 1.2 2005/10/29 20:08:14 kaloyan_raev Exp $

DESTDIR=./recoded
TARGETS=polish \
	english \
	chinese-tr \
	chinese-sim \
	dutch \
	german \
	spanish \
	italian \
	french \
	russian \
	japanese \
	slovak \
	turkish \
	czech \
	portuguese-br \
	swedish \
	afrikaans \
	hungarian

all:	prepare ${TARGETS}
	@for p in ${TARGETS} ; do chmod 644 ${DESTDIR}/$$p.php ; done

prepare:
	@which recode >/dev/null 2>&1 || ( echo "You must have GNU 'recode' installed to use this Makefile,\
										but I could not find it in your path!" && exit 1 )

clean:
	@echo "Nuking recoded lang files..."
	@for p in ${TARGETS} ; do rm -fv ${DESTDIR}/$$p.php ; done

polish:	polish.php
	@echo "Recoding polish..."
	@cat polish.php | recode latin2..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/polish.php
	@chmod 644 polish.php ${DESTDIR}/polish.php

english:	english.php
	@echo "Recoding english..."
	@cat english.php | recode latin1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/english.php
	@chmod 644 english.php ${DESTDIR}/english.php

dutch:	dutch.php
	@echo "Recoding dutch..."
	@cat dutch.php | recode latin1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/dutch.php
	@chmod 644 dutch.php ${DESTDIR}/dutch.php

german:	german.php
	@echo "Recoding german..."
	@cat german.php | recode utf-8..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/german.php
	@chmod 644 german.php ${DESTDIR}/german.php

spanish:	spanish.php
	@echo "Recoding spanish..."
	@cat spanish.php | recode iso-8859-1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/spanish.php
	@chmod 644 spanish.php ${DESTDIR}/spanish.php

italian:	italian.php
	@echo "Recoding italian..."
	@cat italian.php | recode iso-8859-1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/italian.php
	@chmod 644 italian.php ${DESTDIR}/italian.php

chinese-tr:	chinese-tr.php
	@echo "Recoding chinese-tr..."
	@cat chinese-tr.php | recode big5..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/chinese-tr.php
	@chmod 644 chinese-tr.php ${DESTDIR}/chinese-tr.php

chinese-sim:	chinese-sim.php
	@echo "Recoding chinese-sim..."
	@cat chinese-sim.php | recode gb2312..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/chinese-sim.php
	@chmod 644 chinese-sim.php ${DESTDIR}/chinese-sim.php

french:	french.php
	@echo "Recoding french..."
	@cat french.php | recode latin1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/french.php
	@chmod 644 french.php ${DESTDIR}/french.php

japanese:	japanese.php
	@echo "Recoding japanese..."
	@cat japanese.php | recode euc-jp..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/japanese.php
	@chmod 644 japanese.php ${DESTDIR}/japanese.php

russian:	russian.php
	@echo "Recoding russian..."
	@cat russian.php | recode koi8..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/russian.php
	@chmod 644 russian.php ${DESTDIR}/russian.php

slovak:	slovak.php
	@echo "Recoding slovak..."
	@cat slovak.php | recode utf-8..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/slovak.php
	@chmod 644 slovak.php ${DESTDIR}/slovak.php

czech:	czech.php
	@echo "Recoding czech..."
	@cat czech.php | recode cp1250..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/czech.php
	@chmod 644 czech.php ${DESTDIR}/czech.php

turkish:	turkish.php
	@echo "Recoding turkish..."
	@cat turkish.php | recode iso-8859-9..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/turkish.php
	@chmod 644 turkish.php ${DESTDIR}/turkish.php

portuguese-br:	portuguese-br.php
	@echo "Recoding portuguese-br..."
	@cat portuguese-br.php | recode iso-8859-1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/portuguese-br.php
	@chmod 644 portuguese-br.php ${DESTDIR}/portuguese-br.php

swedish:	swedish.php
	@echo "Recoding swedish..."
	@cat swedish.php | recode iso-8859-1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/swedish.php
	@chmod 644 swedish.php ${DESTDIR}/swedish.php

afrikaans:	afrikaans.php
	@echo "Recoding afrikaans..."
	@cat afrikaans.php | recode iso-8859-1..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/afrikaans.php
	@chmod 644 afrikaans.php ${DESTDIR}/afrikaans.php

hungarian:	hungarian.php
	@echo "Recoding hungarian..."
	@cat hungarian.php | recode utf-8..html | \
		awk '{ gsub("&gt;",">"); gsub("&lt;","<"); print $0}' \
		> ${DESTDIR}/hungarian.php
	@chmod 644 hungarian.php ${DESTDIR}/hungarian.php

