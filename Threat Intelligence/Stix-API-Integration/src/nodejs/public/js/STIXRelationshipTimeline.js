/*
 * Copyright (c) 2015 – The MITRE Corporation
 * All rights reserved. See LICENSE.txt for complete terms.
 */

//  A timeline component for d3
//  version v0.1

var StixTimeline = function () { 
    var _self = this;
    
    // chart geometry
    var margin = {
	top: 20, 
	right: 20, 
	bottom: 20, 
	left: 20
    },
    outerWidth = 1175,
    outerHeight = 650,
    width = outerWidth - margin.left - margin.right-150,
    height = outerHeight - margin.top - margin.bottom;
    

    var typeColorMap = {
        "Indicator-Sighting" :"#1abc9c",
        "Incident-First_Malicious_Action" :"#95a5a6",
        "Incident-Initial_Compromise" :"#2ecc71",
        "Incident-First_Data_Exfiltration" :"#9b59b6",
        "Incident-Incident_Discovery" :"#f1c40f",
        "Incident-Incident_Opened" :"#775c2c",
        "Incident-Containment_Achieved" :"#e74c3c",
        "Incident-Restoration_Achieved" :"#e67e22",
        "Incident-Incident_Reported" :"#0080ff",
        "Incident-Incident_Closed" :"#34495e",
        "Incident-COATaken" :"#27ae60"

    };
    
    var legendMap = [
            {type: "Indicator-Sighting", name: "Indicator-Sighting"}, 
            {type:  "Incident-First_Malicious_Action",name: "First Malicious Action"}, 
            {type: "Incident-Initial_Compromise",name: "Initial Compromise"}, 
            {type: "Incident-First_Data_Exfiltration",name: "First Data Exfiltration"}, 
            {type: "Incident-Incident_Discovery",name: "Incident Discovery"}, 
            {type: "Incident-Incident_Opened",name: "Incident Opened"}, 
            {type: "Incident-Containment_Achieved",name: "Containment Achieved"},
            {type: "Incident-Restoration_Achieved",name: "Restoration Achieved"},
            {type: "Incident-Incident_Reported",name: "Incident_Reported"},   
            {type: "Incident-Incident_Closed",name: "Incident Closed"},
            {type: "Incident-COATaken",name: "COATaken"}, 

    ];
    
    var jString = "";

    _self.display = function (jsonString) {
        jString = jsonString;
        
        drawTimeline();
    }
    
    
    _self.resize = function () { 
        //Function exists to avoid errors but if we actually resize here 
        //we will get tons of resize events during a drag resize.
    }
    
    $(window).resize(function () {
        waitForFinalEvent(function(){
          drawTimeline();
        }, 500, "some unique string");
    });
    
    var waitForFinalEvent = (function () {
        var timers = {};
        return function (callback, ms, uniqueId) {
          if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
          }
          if (timers[uniqueId]) {
            clearTimeout (timers[uniqueId]);
          }
          timers[uniqueId] = setTimeout(callback, ms);
        };
      })();

    function drawTimeline()
    {
        d3.select("svg")
        .remove();
        
        var dataset = $.parseJSON(jString);
        timeline()
        .data(dataset)
        .container("#contentDiv")
	.band("mainBand", 0.82)
	.band("naviBand", 0.08)
	.xAxis("mainBand")
	.tooltips("mainBand")
	.xAxis("naviBand")
	.labels("mainBand")
	.labels("naviBand")
	.brush("naviBand", ["mainBand"])
	.redraw();

        d3.select("naviBand")
                .style("display", 'none');
        
    }


    function timeline() {


	//--------------------------------------------------------------------------
	//
	// chart
	//

	// global timeline variables
	var timeline = {},   // The timeline
	data = {},       // Container for the data
	groupedData = [],
	maxGroupSize = 0,
	components = [], // All the components of the timeline for redrawing
	bandGap = 25,    // Arbitray gap between to consecutive bands
	bands = {},      // Registry for all the bands in the timeline
	bandY = 0,       // Y-Position of the next band
	bandNum = 0;     // Count of bands for ids
        
        var svg =     {},
            chart=    {},
            tooltip = {};
        
        timeline.container = function(domElement){
            var newWidth = $('#contentDiv').width();
            outerWidth = newWidth;
            width = outerWidth - margin.left - margin.right-150,
            height = outerHeight - margin.top - margin.bottom;

            // Create svg element
            svg = d3.select(domElement).append("svg")
            .attr("class", "svg")
            .attr("id", "svg")
            .attr("width", outerWidth)
            .attr("height", outerHeight)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top +  ")");

            svg.append("clipPath")
            .attr("id", "chart-area")
            .append("rect")
            .attr("width", width)
            .attr("height", height);

            chart = svg.append("g")
            .attr("class", "chart")
            .attr("clip-path", "url(#chart-area)" )
            .style("overflow", "scroll");

            tooltip = d3.select("body")
            .append("div")
            .attr("class", "tooltip")
            .style("visibility", "visible");
    
            return timeline;
        };

	//--------------------------------------------------------------------------
	//
	// data
	//

	timeline.data = function(items) {

	    var today = new Date(),
	    tracks = [];

	    data.items = items;

	    function calculateTracks(items) {
		var i, k, track;

                
                // younger items end deeper
                items.forEach(function (item) {
                    if(item.track === undefined)
                    {
                        //Find all the other items in this group
                        var groupdedItems = [];
                        groupdedItems.push(item);
                        items.forEach(function (it) {
                            if(item.parentObjId === it.parentObjId && item !== it)
                            {
                                groupdedItems.push(it);
                            }
                        });
                        
                        //Find the end of the window
                        var theEnd = item.end;
                        groupdedItems.forEach(function (it) {
                            if(it.end > theEnd)
                            {
                               theEnd = it.end;
                            }
                        });
                        //Find the start of the window
                        var theStart = item.start;
                        groupdedItems.forEach(function (it) {
                            if(it.start < theStart)
                            {
                               theStart = it.start;
                            }
                        });
                        
                        var groupSize = groupdedItems.length;
                        var fail = false;
                        
                        //We have to move down the track list looking for a window that can fit out group
                        for (i = 0, track = 0; i < tracks.length; i++, track++) {
                            for(k = 0; k < groupSize; k++)
                            {
                                //if the track doesnt exist yet then it is empty and we know it can fit our window
                                if(tracks[i+k])
                                {
                                    if (theStart < tracks[i+k].end && theStart>tracks[i+k].start) {
                                        fail = true;
                                    }
                                    if(theEnd > tracks[i+k].start && theEnd < tracks[i+k].end)
                                    {
                                        fail = true;
                                    }
                                    if(fail)
                                    {
                                        break;
                                    }
                                }
                            }
                            //If we have gotten through a number or tracks equal to our group size
                            //Then we have found a window and can break out
                            if(!fail)
                            {
                                break;
                            }
                        }
                        
                        //This is our starting track
                        var curTrack = track;
                        
                        //We have found the size of out window and the starting track for our window
                        //Now we just add the group items to the track
                        groupdedItems.forEach(function (it) {
                            //If there are already items in this track we add them to the track
                            if(tracks[curTrack])
                            {
                                it.track = curTrack;
                                if(theEnd > tracks[curTrack].end)
                                {
                                    tracks[curTrack].end = theEnd;
                                }
                                if(theStart < tracks[curTrack].start)
                                {
                                    tracks[curTrack].start = theStart;
                                }
                            }
                            //if this is the first item in the track, create new track then add it
                            else
                            {
                                it.track = curTrack;

                                var newTrack={};
                                newTrack.end = theEnd;
                                newTrack.start = theStart;
                                tracks[curTrack] = newTrack;
                            }
                            curTrack++;
                        });
                    }
                });
                
                
                //Figure out the starting track for each group
                items.forEach(function (item) {
                    if(groupedData[item.parentObjId].track === undefined)
                    {
                        groupedData[item.parentObjId].track = item.track;
                    }
                    else{
                        if(item.track < groupedData[item.parentObjId].track)
                        {
                            groupedData[item.parentObjId].track = item.track;
                        }
                    }

                });

	    }
                   
            //A bunch of math to figure out the scale of our data.
	    var maxEnd = null; 
	    var maxStart = null;
	    data.items.forEach(function (item){
		if(maxStart == null)
		{
		    maxStart = item.start;
		}
		if(item.start < maxStart)
		{
		    maxStart = item.start;
		}
		if(item.end < maxStart)
		{
		    maxStart = item.end;
		}


		if(maxEnd == null)
		{
		    if(item.end == null)
		    {
			maxEnd = item.start;
		    }
		    else
		    {
			maxEnd = item.end;
		    }
		}
		if(item.end > maxEnd)
		{
		    maxEnd = item.end;
		}
		if(item.start > maxEnd)
		{
		    maxEnd = item.start;
		}
	    });

	    var ed = new Date(maxEnd);
	    var sd = new Date(maxStart);
	    var ts = ed.getTime()-sd.getTime();
	    //InstantOffset is How big an instant dot appears on the timeline
	    //var instantOffset = Math.pow(10, ts.toString().length-1);
            var instantOffset = ts/10;
            
	    // Convert yearStrings into dates
	    data.items.forEach(function (item){
		if (item.end == null || item.end == "" || item.end==item.start) {
		    //console.log("1 item.start: " + item.start);
		    //console.log("2 item.end: " + item.end);
		    item.start = parseDate(item.start);
		    item.end = new Date(item.start.getTime() + instantOffset);
		    //console.log("3 item.end: " + item.end);
		    item.instant = true;
		} else {
		    //console.log("4 item.end: " + item.end);
		    item.start = parseDate(item.start);
		    item.end = parseDate(item.end);
		    item.instant = false;
		}
		// The timeline never reaches into the future.
		// This is an arbitrary decision.
		// Comment out, if dates in the future should be allowed.
		/*if (item.end > today) {
		    item.end = today
		    };*/
	    });

	    //Group the events
	    data.items.forEach(function (item){
		if(groupedData.hasOwnProperty(item.parentObjId))
		{
		    var group = groupedData[item.parentObjId];
		    group.count++;
		    if(item.start < group.start)
		    {
			group.start = item.start;
		    }
		    if(item.end > group.end)
		    {
			group.end = item.end;
		    }
		}
		else{
		    var group = {};
		    group.count = 1;
		    group.start = item.start;
		    if(item.instant == true)
		    {
			group.end = 0;
			group.hasinstant = true;
		    }
		    else
		    {
			group.end = item.end;
		    }
		    groupedData[item.parentObjId] = group;

		}
	    });



	    for (var k in groupedData) {
		if(maxGroupSize < groupedData[k].count)
		{
		    maxGroupSize = groupedData[k].count;

		}
	    }


	    calculateTracks(data.items, "descending", "backward", "parentid");

	    data.nTracks = tracks.length;
	    data.minDate = d3.min(data.items, function (d) {
		return d.start;
	    });
	    data.maxDate = d3.max(data.items, function (d) {
		return d.end;
	    });

            outerHeight = (24*data.nTracks)+200;
            
	    return timeline;
	};

	//----------------------------------------------------------------------
	//
	// band
	//

	timeline.band = function (bandName, sizeFactor) {
	    var band = {};
	    var printedGroupSize = {};
	    band.id = "band" + bandNum;
	    band.x = 0;
	    band.y = bandY;
	    band.w = width;
	    band.trackOffset = 4;
            if(bandName==='mainBand')
            {
                band.trackHeight = 20;
                band.h = (band.trackHeight+band.trackOffset)*data.nTracks;
            }
            else if(bandName==='naviBand')
            {
                band.h = 75;
                band.trackHeight = Math.min((band.h - band.trackOffset) / data.nTracks, 20);
                
            }
            
	    band.itemHeight = band.trackHeight,
	    band.parts = [],
	    band.instantWidth = 100; // arbitray value
	    band.xScale = d3.time.scale()
	    .domain([data.minDate, data.maxDate])
	    .range([0, band.w]);


	    band.yScale = function (track) {
		return band.trackOffset + track * band.trackHeight;
	    };

	    band.g = chart.append("g")
	    .attr("id", band.id)
	    .attr("transform", "translate(0," + band.y +  ")")
            .style("fill", "white");

	    band.g.append("rect")
	    .attr("class", "band")
	    .attr("width", band.w)
	    .attr("height", band.h)
            .style("fill", "white");


       

             // add legend   
            var legend = svg.append("g")
              .attr("class", "legend")
              .attr("x", outerWidth -180)
              .attr("y", 25)
              .attr("height", 100)
              .attr("width", 100)

         
         var borderPath = svg.append("rect")
              .attr("x", outerWidth -185)
              .attr("y", 0)
              .attr("height", 190)
              .attr("width", 160)
            .style("stroke", "black")
            .style("fill", "none")
            .style("stroke-width", 1);
            

            legend.selectAll('g')
                .data(legendMap)
                .enter()
                .append('g')
                .each(function(d, i) {
                  var g = d3.select(this);
                  g.append("rect")
                    .attr("x", outerWidth - 180)
                    .attr("y", i*15+25)
                    .attr("width", 10)
                    .attr("height", 10)
                    .style("fill", typeColorMap[d.type])
                    .on("click", function(){
                        var t = d3.select(this);    
                        if(t.style("fill") === "#000000")
                            d3.select(this).style("fill", typeColorMap[d.type]);
                        else
                            d3.select(this).style("fill", "#000000");
                        var b = d3.selectAll("#" + d.type);
                        b.style("display", function(d){
                            var e = d3.select(this).style('display');
                            if(e === 'none')
                            {
                                return 'block';
                            }
                            else
                            {
                                return 'none';
                            }
                        });
                  });

                  g.append("text")
                    .attr("x", outerWidth - 170)
                    .attr("y", i * 15+34)
                    .attr("height",30)
                    .attr("width",100)
                    .style("fill", typeColorMap[d.type])
                    .text(d.name);

                });
                

                legend.append("text")                    
                .attr("x", outerWidth -180)
                .attr("y", 15)
                .attr("height", 100)
                .attr("width", 100)
                .style("fill", "black")
                .text("Click to hide types");
                
            
	    // Items
            //TODO: The groups should be the items passed, but unsure how this is processed
	    var items = band.g.selectAll("g")
	    .data(data.items)
	    .enter().append("svg")
	    .attr("y", function (d) {
		var numPrinted = 0;
		if(!printedGroupSize.hasOwnProperty(d.parentObjId))
		{
		    printedGroupSize[d.parentObjId] = 1;
		}
		else
		{
		   numPrinted = printedGroupSize[d.parentObjId];
		   printedGroupSize[d.parentObjId]++; 
		}
                return (band.yScale(d.track));

	    })
	    .attr("height", band.itemHeight)
	    .attr("class", function (d) {
		return d.instant ? "part instant" : "part interval";
	    });


	    //Groups
	    var groupings = band.g.selectAll("g")
	    .data(data.items)
	    .enter().append("svg")
	    .attr("y", function (d) {
		return band.yScale(groupedData[d.parentObjId].track);

	    })
	    .attr("height", function (d) {
		var numPrinted = printedGroupSize[d.parentObjId];
		return band.trackHeight * numPrinted;
	    })
	    .attr("class", "part grouping");


	    var groups = d3.select("#band0").selectAll(".grouping");
	    groups.append("rect")
	    .style("fill", "none")
	    .attr("width", "100%")
	    .attr("height", "93%")
	    .style("stroke", "black")
	    .style("stroke-width", function (d) {
		if(groupedData[d.parentObjId].count > 1)
		{
		    return 2;
		}
		else
		{
		    return 0;
		}
	    })

	    var intervals = d3.select("#band" + bandNum).selectAll(".interval");
	    intervals.append("rect")
	    .style("fill", function (d) {
		return typeColorMap[d.type];
	    })
	    .attr("width", "100%")
	    .attr("height", "90%")
            .attr("id", function(d){
              return d.type;  
            });

	    intervals.append("text")
	    .attr("class", "intervalLabel")
	    .attr("x", 1)
	    .attr("y", 10)
            .style("fill", 'black')
	    .text(function (d) {
		return d.description.substring(0,12);
	    })
            .attr("id", function(d){
              return d.type;  
            });

	    var instants = d3.select("#band" + bandNum).selectAll(".instant");
	    instants.append("circle")
	    .style("fill", function (d) {
		return typeColorMap[d.type];
	    })
	    .attr("cx", band.itemHeight / 2)
	    .attr("cy", band.itemHeight / 2)
	    .attr("r", 5)
            .attr("id", function(d){
              return d.type;  
            });

	    instants.append("text")
	    .attr("class", "instantLabel")
	    .attr("x", 15)
	    .attr("y", 10)
            .style("fill", 'black')
	    .text(function (d) {
                return d.description.substring(0,12);
	    })
            .attr("id", function(d){
              return d.type;  
            });
            
	    band.addActions = function(actions) {
		// actions - array: [[trigger, function], ...]
		actions.forEach(function (action) {
		    items.on(action[0], action[1]);
		})
	    };

	    band.redraw = function () {
		items
		.attr("x", function (d) {
		    return band.xScale(d.start);
		})
		.attr("width", function (d) {
		    return band.xScale(d.end) - band.xScale(d.start);
		});
		band.parts.forEach(function(part) {
		    part.redraw();
		});

		groupings
		.attr("x", function (d) {
		    //return band.xScale(d.start);
		    return band.xScale(groupedData[d.parentObjId].start);
		})
		.attr("width", function (d) {
		    //return band.xScale(d.end) - band.xScale(d.start);
		    var width = band.xScale(groupedData[d.parentObjId].end) - band.xScale(groupedData[d.parentObjId].start);
		    if(groupedData[d.parentObjId].hasinstant && width < 15)
		    {
			return 15;
		    }
		    return band.xScale(groupedData[d.parentObjId].end) - band.xScale(groupedData[d.parentObjId].start);
		});
		band.parts.forEach(function(part) {
		    part.redraw();
		})
	    };
            
	    bands[bandName] = band;
	    components.push(band);
	    // Adjust values for next band
	    bandY += band.h + bandGap;
	    bandNum += 1;

	    return timeline;
	};

	//----------------------------------------------------------------------
	//
	// labels
	//

	timeline.labels = function (bandName) {

	    var band = bands[bandName],
	    labelWidth = 46,
	    labelHeight = 20,
	    labelTop = band.y + band.h - 10,
	    y = band.y + band.h + 1,
	    yText = 15;

	    var labelDefs = [
	    ["start", "bandMinMaxLabel", 0, 4,
	    function(min, max) {
		return displayDateMinMax(min);
	    },
	    "Start of the selected interval", band.x + 30, labelTop],
	    ["end", "bandMinMaxLabel", band.w - labelWidth, band.w - 4,
	    function(min, max) {
		return displayDateMinMax(max);
	    },
	    "End of the selected interval", band.x + band.w - 152, labelTop]
	    ];

	    var bandLabels = chart.append("g")
	    .attr("id", bandName + "Labels")
	    .attr("transform", "translate(0," + (band.y + band.h + 1) +  ")")
	    .selectAll("#" + bandName + "Labels")
	    .data(labelDefs)
	    .enter().append("g")
	    .on("mouseover", function(d) {
		tooltip.html(d[5])
		.style("top", d[7] + "px")
		.style("left", d[6] + "px")
		.style("visibility", "visible");
	    })
	    .on("mouseout", function(){
		tooltip.style("visibility", "hidden");
	    });

	    bandLabels.append("rect")
	    .attr("class", "bandLabel")
	    .attr("x", function(d) {
		return d[2];
	    })
	    .attr("width", labelWidth)
	    .attr("height", labelHeight)
            .style("fill", "white")
	    .style("opacity", 1);

	    var labels = bandLabels.append("text")
	    .attr("class", function(d) {
		return d[1];
	    })
	    .attr("id", function(d) {
		return d[0];
	    })
	    .attr("x", function(d) {
		return d[3];
	    })
	    .attr("y", yText)
            .style("fill", 'black')
	    .attr("text-anchor", function(d) {
		return d[0];
	    });

	    labels.redraw = function () {
		var min = band.xScale.domain()[0],
		max = band.xScale.domain()[1];

		labels.text(function (d) {
		    return d[4](min, max);
		})
	    };

	    band.parts.push(labels);
	    components.push(labels);

	    return timeline;
	};

	//----------------------------------------------------------------------
	//
	// tooltips
	//

	timeline.tooltips = function (bandName) {

	    var band = bands[bandName];

	    band.addActions([
		// trigger, function
		["mouseover", showTooltip],
		["mouseout", hideTooltip],
		["contextmenu", showContextTimeline]
		]);

	    function getHtml(element, d) {
		var html;
		if (element.attr("class") === "part interval") {
		    html = getTooltip(d) + "<br>" + displayDateLabel(d.start) + " - " + displayDateLabel(d.end);
		} else {
		    html = getTooltip(d) + "<br>" + displayDateLabel(d.start);
		}
		return html;
	    }

	    function showTooltip (d) {

		var x = event.pageX < band.x + band.w / 2
		? event.pageX + 10
		: event.pageX - 110,
		y = event.pageY < band.y + band.h / 2
		? event.pageY + 30
		: event.pageY - 30;

		tooltip
		.html(getHtml(d3.select(this), d))
		.style("top", y + "px")
		.style("left", x + "px")
		.style("visibility", "visible");
	    }

	    function hideTooltip () {
		tooltip.style("visibility", "hidden");
	    }

            /** Show the context menu for showing the HTML view when right clicking a node
             * 
             * @param data The node that was clicked
             */
	    function showContextTimeline (data) {
                position = d3.mouse(this);
		offset = $(this).offset();
		scrollTop = 10; 
		showContext(data,(position[0]+offset.left+(10/2))+'px',(position[1]+offset.top-50+scrollTop)+'px');


	    }
	    return timeline;
	};

	//----------------------------------------------------------------------
	//
	// xAxis
	//

	timeline.xAxis = function (bandName, orientation) {

	    var band = bands[bandName];

	    var axis = d3.svg.axis()
	    .scale(band.xScale)
	    .orient(orientation || "bottom")
	    .tickSize(6, 0)
	    .tickFormat(function (d) {
		return displayDateTicks(d);
	    });

	    var xAxis = chart.append("g")
	    .attr("class", "axis")
	    .attr("transform", "translate(0," + (band.y + band.h)  + ")")
            //.style({ 'stroke': 'Black', 'opacity': 1});

	    xAxis.redraw = function () {
		xAxis.call(axis)
                .selectAll(".tick,.domain")
                .style("stroke", "black")
                .style("fill", "white")
                .style("opacity", 1);
	    };

	    band.parts.push(xAxis); // for brush.redraw
	    components.push(xAxis); // for timeline.redraw

	    return timeline;
	};

	//----------------------------------------------------------------------
	//
	// brush
	//

	timeline.brush = function (bandName, targetNames) {

	    var band = bands[bandName];

	    var brush = d3.svg.brush()
	    .x(band.xScale.range([0, band.w]))
	    .on("brush", function() {
		var domain = brush.empty()
		? band.xScale.domain()
		: brush.extent();
		targetNames.forEach(function(d) {
		    bands[d].xScale.domain(domain);
		    bands[d].redraw();
		});
	    });

	    var xBrush = band.g.append("svg")
	    .attr("class", "x brush")
	    .call(brush);

	    xBrush.selectAll("rect")
	    .attr("y", 4)
	    .attr("height", band.h - 4);

	    return timeline;
	};

	//----------------------------------------------------------------------
	//
	// redraw
	//

	timeline.redraw = function () {     
            components.forEach(function (component) {
		component.redraw();
	    });
	};

	//--------------------------------------------------------------------------
	//
	// Utility functions
	//

	function parseDate(dateString) {

	    var date = new Date(dateString);

	    if (date !== null) 
	    {
		return date;
	    }

	}
    
	function displayDateLabel(date) {
	    return date.toLocaleString();
	}
    
	function displayDateTicks(date) {
	    var month = date.getMonth()+1;
	    var day = date.getDate();
	    var year = date.getFullYear();

	    return month + "/" + day + "/" + year;
	}
    
	function displayDateMinMax(date) {
	    var month = date.getMonth();
	    var day = date.getDate();
	    var year = date.getFullYear();

	    return month + "/" + day + "/" + year;
	}
    
	function getTooltip(d) {
	    var ttStr = '';
            if(d.type)
	    {
		ttStr += "Parent ID: " + d.parentObjId + "<br>";
		ttStr += "Description: " +d.description+ "<br>";
		ttStr += "Event Type: " +htmlSectionMap[d.type];
                ttStr += "<br>Track: " + d.track;
	    }
	    return ttStr;
	}


	return timeline;
    }

};
