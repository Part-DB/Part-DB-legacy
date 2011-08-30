#!/usr/bin/env python
"""
# Part-DB autoprice - price fetcher
#
# (c) 2009 Michael Buesch <mb@bu3sch.de>
#
# Licensed under the GNU/GPL version 2 or (at your option) any later version
"""
VERSION = "005"

import sys
import getopt
try:
	import MySQLdb
except ImportError:
	print "Please install the python MySQLdb module"
	print "On Debian Linux run:  apt-get install python-mysqldb"
	sys.exit(1)
import httplib
import socket
import urllib
import re

opt_hostname = "localhost"
opt_user = "root"
opt_database = "partdb"
opt_password = None
opt_missingOnly = False
opt_override = False

def usage():
	print "Part-DB autoprice - price fetcher - version " + VERSION
	print ""
	print "Usage: %s [OPTIONS]" % sys.argv[0]
	print ""
	print "-H|--host HOSTNAME    Use HOSTNAME as MySQL host (defaults to localhost)"
	print "-u|--user USER        Use USER as MySQL user (defaults to root)"
	print "-p|--password PASS    Use password PASS (defaults to prompt)"
	print "-d|--database DB      Use the database DB (defaults to partdb)"
	print "-m|--missing          Fetch and fill missing prices only (default off)"
	print "-o|--override         Always override prices, if no new price available (default off)"
	print "-h|--help             Print this help text"

try:
	(opts, args) = getopt.getopt(sys.argv[1:],
		"hH:u:p:d:mo",
		[ "help", "host=", "user=", "password=", "database=",
		  "missing", "override", ])
except getopt.GetoptError:
	usage()
	sys.exit(1)
for (o, v) in opts:
	if o in ("-h", "--help"):
		usage()
		sys.exit(0)
	if o in ("-H", "--host"):
		opt_hostname = v
	if o in ("-u", "--user"):
		opt_user = v
	if o in ("-p", "--password"):
		opt_password = v
	if o in ("-d", "--database"):
		opt_database = v
	if o in ("-m", "--missing"):
		opt_missingOnly = True
	if o in ("-o", "--override"):
		opt_override = True
if opt_password is None:
	opt_password = raw_input("MySQL password: ")


defaultHttpHeader = {
	"User-Agent" : "Mozilla/5.0 (X11; U; Linux ppc; en-US; rv:1.9.0.12) " +\
		       "Gecko/2009072221 Iceweasel/3.0.6 (Debian-3.0.6-1)",
	"Accept" : "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
	"Accept-Language" : "en-us,en;q=0.5",
	"Accept-Charset" : "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
	"Keep-Alive" : "300",
	"Connection" : "keep-alive",
}

reicheltSessionId = None
pollinSessionId = None

class NoPriceError(Exception): pass
class PartNrError(Exception): pass

def removeChars(string, template):
	for t in template:
		string = string.replace(t, "")
	return string

def getReicheltSessionId():
	global reicheltSessionId

	if reicheltSessionId is not None:
		return reicheltSessionId

	print "Fetching Reichelt session ID"
	for i in range(0, 5): # Try five times.
		http = httplib.HTTPConnection("www.reichelt.de")
		http.request("GET", "/")
		resp = http.getresponse().read()
		m = re.compile(r'.*SID=(\w{53,53}).*').search(resp)
		if m:
			break
	if not m:
		print "Failed to get Reichelt session ID"
		sys.exit(1)
	reicheltSessionId = m.group(1)

	return reicheltSessionId

def fetchReicheltPrice(partName, partNr):
	# Fetch the price for an item from Reichelt.
	# The Reichelt search feature is not reliable, so we abuse
	# the shopping basket for our purpose. We put the item into the basket,
	# read the basket sum and remove it from the basket again.

	print "Fetching Reichelt price for \"%s\" \"%s\"" % (partName, partNr)

	if not partNr:
		print "No part number"
		raise NoPriceError()

	# Put the item into the shopping basket
	http = httplib.HTTPConnection("www.reichelt.de")
	body = "Input_Unknown%5B0%5D=" + urllib.quote_plus(partNr) +\
	       "&Anzahl_Unknown%5B0%5D=&insert=WK+aktualisieren"
	header = defaultHttpHeader.copy()
	header["Host"] = "www.reichelt.de"
	header["Cookie"] = "USERDOMAIN=www.reichelt.de; last_viewed_articles[0]=23445; " +\
			   "Reichelt_SID=" + getReicheltSessionId() + "; Reichelt_PROVID=NewSID"
	header["Content-Type"] = "application/x-www-form-urlencoded"
	header["Content-Length"] = str(len(body))
	http.request("POST", "/Warenkorb/index.html?SID=" + getReicheltSessionId() + ";ACTION=5;SORT=user;",
		     body, header)
	basket = http.getresponse().read()
	basket = removeChars(basket, "\r\n")

	# Remove the item from the shopping basket
	body = "Input_Unknown%5B0%5D=Artikel-Direkteingabe&Anzahl_Unknown%5B0%5D=&" +\
	       "anzahl_alt%5B45024%5D=1&anzahl%5B45024%5D=1&Delete%5B_all_%5D=WK+l%F6schen"
	header["Content-Length"] = str(len(body))
	http.request("POST", "/Warenkorb/index.html?SID=" + getReicheltSessionId() + ";ACTION=5;SORT=user;",
		     body, header)
	http.getresponse() # discard result

	# Parse the shipping basket sum
	r = re.compile(r".+Summe: (\d+,\d+) &euro;.+")
	m = r.match(basket)
	if not m:
		print "Failed to parse Reichelt basket sum"
		raise NoPriceError()
	price = m.group(1)
	if price == "0,00":
		print "Failed to put the item into the basket"
		raise PartNrError() # Most likely caused by invalid partNr
	price = price.replace(",", ".")
	try:
		price = float(price)
	except ValueError:
		print "Got price, but it doesn't seem to be a float number: %s" % price
		raise NoPriceError()
	print "Got %.2f EUR" % price

	return price

def fetchConradPrice(partName, partNr):
	print "Conrad price fetcher not implemented, yet"
	#TODO not implemented, yet
	raise NoPriceError()

def getPollinSessionId():
	global pollinSessionId

	if pollinSessionId is not None:
		return pollinSessionId

	print "Fetching Pollin session ID"
	for i in range(0, 5): # Try five times.
		http = httplib.HTTPConnection("www.pollin.de")
		http.request("GET", "/shop/index.html")
		resp = http.getresponse()
		c = resp.getheader("Set-Cookie")
		if c:
			m = re.compile(r'PHPSESSID=(\w+).*').match(c)
			if m:
				pollinSessionId = m.group(1)
				break
	if pollinSessionId is None:
		print "Failed to get Pollin session ID"
		sys.exit(1)

	return pollinSessionId

def fetchPollinPrice(partName, partNr):
	# Fetch the price for an item from Pollin.
	# We abuse the shopping basket for our purpose. We put the item into the
	# basket read the basket sum and remove it from the basket again.

	print "Fetching Pollin price for \"%s\" \"%s\"" % (partName, partNr)

	if not partNr:
		print "No part number"
		raise NoPriceError()

	partNr = partNr.split("-")
	if len(partNr) == 1:
		wkz = ""
		bestellnr = partNr[0]
	elif len(partNr) == 2:
		wkz = partNr[0]
		bestellnr = partNr[1]
	else:
		print "Invalid part number format (must be  00-000 000  or  000 000)"
		raise NoPriceError()
	wkz = removeChars(wkz, "\r\n\t ")
	bestellnr = removeChars(bestellnr, "\r\n\t ")

	# Put the item into the shopping basket
	http = httplib.HTTPConnection("www.pollin.de")
	body = "do_anzahl_0=1&do_wkz_0=" + urllib.quote_plus(wkz) +\
	       "&do_bestellnr2_0=" + urllib.quote_plus(bestellnr)
	header = defaultHttpHeader.copy()
	header["Host"] = "www.pollin.de"
	header["Cookie"] = "PHPSESSID=" + getPollinSessionId() + "; pollincookie=1"
	header["Content-Type"] = "application/x-www-form-urlencoded"
	header["Content-Length"] = str(len(body))
	http.request("POST", "/shop/warenkorb.html HTTP/1.1", body, header)
	basket = http.getresponse().read()
	basket = removeChars(basket, "\r\n")

	# Remove the item from the shopping basket
	body = "remoteAction=deleteRemote&type=basket&" +\
	       "items=%5B%7B%22artnrKurz%22%3A%22" + bestellnr + "%22%2C%22" +\
	       "menge%22%3A%221%22%2C%22selected%22%3Atrue%2C%22itemRow%22%3A%220%22%7D%5D"
	header["Content-Length"] = str(len(body))
	http.request("POST", "/shop/ajax.html HTTP/1.1", body, header)
	http.getresponse() # discard result

	# Parse the shipping basket sum
	r = re.compile(r".+<small>1 Artikel: (\d+,\d+) &euro;</small>.+")
	m = r.match(basket)
	if not m:
		print "Failed to parse Pollin basket sum"
		raise PartNrError() # Most likely caused by invalid partNr
	price = m.group(1)
	price = price.replace(",", ".")
	try:
		price = float(price)
	except ValueError:
		print "Got price, but it doesn't seem to be a float number: %s" % price
		raise NoPriceError()
	print "Got %.2f EUR" % price

	return price


try:
	conn = MySQLdb.connect(host = opt_hostname, user = opt_user,
			       passwd = opt_password, db = opt_database)
	cursor = conn.cursor()

	# Get the supplier IDs
	reicheltId = None
	conradId = None
	pollinId = None
	cursor.execute("SELECT id,name FROM suppliers")
	for supplier in cursor.fetchall():
		id = supplier[0]
		name = supplier[1].lower().strip()
		if name == "reichelt":
			if reicheltId is not None:
				print "Found multiple Reichelt suppliers"
				sys.exit(1)
			reicheltId = id
		if name == "conrad":
			if conradId is not None:
				print "Found multiple Conrad suppliers"
				sys.exit(1)
			conradId = id
		if name == "pollin":
			if pollinId is not None:
				print "Found multiple Pollin suppliers"
				sys.exit(1)
			pollinId = id

	# Get the parts list
	cursor.execute("SELECT id,name,id_supplier,supplierpartnr FROM parts")
	allParts = cursor.fetchall()

	for part in allParts:
		partId = part[0]
		partName = part[1].strip()
		supplier = part[2]
		supplierPartNr = part[3].strip()
 
 		if opt_missingOnly:
			cursor.execute("SELECT preis FROM preise WHERE part_id=%s" % partId)
			price = cursor.fetchone()
			if price is not None:
				if float(price[0]) > 0.001:
					continue # There's already a price for the item

 		price = 0.0
		try:
	 		if reicheltId is not None and supplier == reicheltId:
				price = fetchReicheltPrice(partName, supplierPartNr)
	 		elif conradId is not None and supplier == conradId:
				price = fetchConradPrice(partName, supplierPartNr)
	 		elif pollinId is not None and supplier == pollinId:
				price = fetchPollinPrice(partName, supplierPartNr)
		except NoPriceError, e: pass
		except PartNrError, e:
			print "Supplier part number \"" + supplierPartNr +\
			      "\" for part \"" + partName + "\" probably is invalid"
		if price > 0.001 or opt_override:
			cursor.execute("DELETE FROM preise WHERE part_id=%d LIMIT 1;" % partId)
			if price > 0.001:
				cursor.execute("INSERT INTO preise (part_id,ma,preis,t) VALUES (%s, 1, %f, NOW());" %\
					       (partId, price))
			else:
				print "Deleted price for part %s" % partName
		print ""

	if 0: # Debug dump
		cursor.execute("SELECT id,part_id,ma,preis FROM preise")
		for price in cursor.fetchall():
			print price

except MySQLdb.MySQLError, e:
	print "MySQL error:"
	print e
	sys.exit(1)
except socket.error, e:
	print "Socket error:"
	print e
	sys.exit(1)
except httplib.HTTPException, e:
	print "HTTP error:"
	print e.__class__
	sys.exit(1)
