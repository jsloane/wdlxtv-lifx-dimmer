#!/bin/sh
###
# This script continuously polls the media state to detect a change and perform an action, until the lock file is removed
###

LOCKFILE="/apps/lifxdimmer/lifxdimmer.lock"
CONFFILE="/apps/lifxdimmer/conf/lifxdimmer.conf"

logMessage () {
	# if debug enabled
	DATE=`date +'%Y-%m-%d %H:%M:%S'`
	echo "$DATE $1" >> /apps/lifxdimmer/logs/lifxdimmer.log
	#logger -t "lifxdimmer" $1
}

# Check for lock file.
# If the file exists check the pid is still running and exit
# Otherwise create the pid file and continue
if [ -e $LOCKFILE ]; then 
	# check if script already running
	PID=`cat $LOCKFILE`
	if [ -e /proc/${PID} -a /proc/${PID}/exe ]; then
		logMessage "Service already running, exiting."
		exit 1
	fi
	# Set the process as owner of the lock
	echo "$$" > $LOCKFILE
else
	# lock file not found, make one and own the lock.
	echo "$$" > $LOCKFILE
	chmod 777 $LOCKFILE
fi

echo ""
logMessage "Starting process."
logMessage "Lock file: $LOCKFILE"

# read in config
source $CONFFILE
logMessage "Config file: $CONFFILE"
logMessage "Host: $CONFHOST"
logMessage "Bulb Group ID: $CONFBULBGROUP"
logMessage "Brightness value: $CONFBRIGHT"
logMessage "Dim value: $CONFDIM"

# check configuration
if [ -z "$CONFHOST" ]  ; then
	logMessage "Host not set, exiting."
	exit 0
fi
if [ -z "$CONFBULBGROUP" ]  ; then
	logMessage "Bulb Group ID not set, exiting."
	exit 0
fi
if [ -z "$CONFBRIGHT" ]  ; then
	logMessage "Brightness value not set, exiting."
	exit 0
fi
if [ -z "$CONFDIM" ]  ; then
	logMessage "Dim value not set, exiting."
	exit 0
fi

onExit() {
	logMessage "Kill signal received. Stopping process."
}
trap onExit EXIT

lifxDim () {
	logMessage "Action: dim"
	# curl...
}

lifxBrighten () {
	logMessage "Action: brighten"
	# curl...
}

sleep 5
logMessage "Polling media state."

while [ -e /apps/lifxdimmer/lifxdimmer.lock ]
do
	POWER=$(cat /proc/led)

	if [ "$POWER" == "OFF" ] ; then
		sleep 10
	elif [ "$POWER" == "ON" ] ; then
		### get playing state
		STATE=`upnp-cmd GetTransportInfo | grep CurrentTransportState | awk '{print $3}'`
		
		### check if we have the previous state
		if [ -z "$PREVIOUSSTATE" ] ; then
			### no state, set previous state to state
			PREVIOUSSTATE=$STATE
		elif [ "$PREVIOUSSTATE" != "$STATE" ] ; then
			### check if the state has changed 
			logMessage "State change detected."
			logMessage "STATE: $STATE"
			logMessage "PREVIOUSSTATE: $PREVIOUSSTATE"
			
			### check this is a video
			MEDIAINFO=`upnp-cmd GetMediaInfo | grep CurrentURIMetaData | grep ":video/"`
			
			if [ ! -z "$MEDIAINFO" ] ; then
				logMessage "Video media detected"
				if [ "$STATE" == "PLAYING" ] ; then
					lifxDim
				elif [ "$STATE" == "PAUSED_PLAYBACK" ] ; then
					lifxBrighten
				elif [ "$STATE" == "STOPPED" ] ; then
					lifxBrighten
				fi
			fi
			
			PREVIOUSSTATE=$STATE
		fi
	fi
	
	sleep 1
done

logMessage "Stopping process."
