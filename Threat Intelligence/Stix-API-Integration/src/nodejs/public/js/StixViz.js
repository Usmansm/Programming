/*
 * Copyright (c) 2015 - The MITRE Corporation
 * All rights reserved. See LICENSE.txt for complete terms.
 * 
 * This file contains the functionality for determining relationships specified in the xml
 * files loaded.  The top level function is generateTreeJson(inputXMLFiles)
 * 
 * Json is created representing the nodes and links in the tree.   This is passed to 
 * displayTree(json) for display in STIXViz.
 * 
 */

var path = require('path');

var xmlDocs = {}, docIndex = 0;

var working = 0;

var view = null,
viewType = null,
jsonDataObj = null,  // json returned from generateJson
relationshipData=null,  // stringified relationship json
timelineData = null,   // stringified timeline json
layout=null;



$(function () { 
	/**
	 *  Add handler for file select input
	 */
	$('#files').on('change', function () { handleFileSelect($(this)); });
	

	/**
	 *  Initialize the page layout. North section is nav menu, center is the tree view, south is the HTML view
	 */
	layout = $('body').layout({ 
		defaults: { 
			resizable:true,
			fxName:'slide',
			fxSpeed:'slow'
		},
		north: { 
			size:"auto",
			spacing_open:			0,			// cosmetic spacing
			togglerLength_open:		0,			// HIDE the toggler button
			togglerLength_closed:	-1,			// "100%" OR -1 = full width of pane
			resizable: 				false,
			slidable:				false,		//	override default effect
			fxName:					"none"
		},
		center: { 
			minSize:400
		},
		south: {
			initClosed:true,
			size:300,
			onshow: function () { view && view.resize(); },
			onhide: function () { view && view.resize(); },
			onopen: function () { view && view.resize(); },
			onclose: function () { view && view.resize(); },
			onresize: function () { view && view.resize(); }
		} 			
	});
	
	viewType = "selectView-graph";   // Default to tree view
	view = new StixGraph();  
	

	/**
	 * When the window is resized, resize and update the tree 
	 */
	$(window).resize(function () {
        waitForFinalEvent(function(){
          view.resize();
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
	

	
	/**
	 * Handler for Show HTML context menu
	 */
	$('#contextMenu #showHtml').on('click',function () {
		$("#contextMenu").hide();
		showHtmlByContext(contextNode);
	});
	
	
	$('#viewList li').on('click', function () {
		viewType = $(this).attr('id');
		$('#selectedView').html($(this).text() + '<b class="caret"></b>');
		reset('view');
		if (viewType === 'selectView-tree') { 
			$(filterDiv).hide();
			$('#viewName').text('STIX Tree View');
			view = new StixTree();
			if (relationshipData) { 
				view.display(relationshipData);
				layout.resizeAll();
			};
		} else if (viewType === 'selectView-graph') {
			$(filterDiv).show();
			$('#viewName').text('STIX Graph View');
			view = new StixGraph();
			if (relationshipData) { 
				view.display(relationshipData);
				layout.resizeAll();
			};
		} else if (viewType === 'selectView-timeline'){
			$(filterDiv).hide();
			$('#viewName').text('STIX Timeline View');
			view = new StixTimeline();
			if (timelineData) {
				view.display(timelineData);
				layout.resizeAll();
			}
		};

	});

      
	/**
	 * If there's a context menu open, you can hide it by clicking somewhere else in the document
	 */
	$(document).click(function () { 
		$('#contextMenu').hide();
	});
	
	
});

/**
 * Add an XML document to the list of documents included in the tree display and process the XSLT transform
 * for that document
 * @param f
 * @param xml
 */
function addXmlDoc (f) { 

	working++;
	var num = docIndex++;
	
	xmlDocs[num] = {name:f.name};
	
	xmlFilePath = f.path.replace(/\\/g,'\\\\\\\\');
	xslFilePath = path.resolve("public/xslt/stix_to_html.xsl").replace(/\\/g,'\\\\\\\\');
	
	
	// Construct top level menu for displaying HTML view of XML files
	$('#xmlFileList').append('<li><a id="xmlFile-'+num+'" href="#">'+f.name+'</a></li>');

	$('#xmlFile-'+num).on("click", function () {
		doc = xmlDocs[$(this).attr("id").split("-")[1]];
		if (doc) { 
			showProcessing();
			var totalTime = 0;
			var waitForXslt = setInterval(function () { // wait until xslt processing is complete
				if (working == 0) { 
					clearInterval(waitForXslt);
					endProcessing();
					if (typeof doc.html === 'undefined') {  // Transformed the XML to HTML
						showHtml("<div id='wrapper'><h2>Could not convert XML file to HTML. Make sure you have java.exe on your path.</h2></div>");
					} else {  // The transform failed
						showHtml(new XMLSerializer().serializeToString($(doc.html).find('#wrapper').get(0)));
					}
				} else if (totalTime > 30000) { // wait for max of 30 seconds 
					clearInterval(waitForXslt);
					endProcessing();
					showHtml("<div id='wrapper'><h2>Could not convert XML file to HTML.</h2></div>");
				} else { 
					totalTime += 200;
				}
			}, 200);
		} else { 
			showHtml("<div id='wrapper'><h2>Could not convert XML file to HTML</h2></div>");
		}
		$('#htmlView').scrollTop(0);
    });

	try {
		instance.sendXsltRequest(num,xmlFilePath,xslFilePath);
	} catch (exception) { 
		console.log("error transforming xml: " + exception.message);
		working = 0;
	}
	
	
	
}

// callback function for generateJson, which is called from handlefileselect
//  jsonDataObj created by generateJson contains a child for each type of view
//    child json is stringified into global vars for later use when switching views
function displayJson(jsonDataObj, viewType) {
	$.fn.filterDivReset();
	$(filterDiv).show();
	relationshipData = JSON.stringify(jsonDataObj["relationshipData"], null, 2);
	timelineData = JSON.stringify(jsonDataObj["timelineData"], null, 2);
	if ((viewType === 'selectView-tree') || (viewType === 'selectView-graph')) {
                $(filterDiv).show();
		view.display(relationshipData);
		layout.resizeAll();
	} else if (viewType === 'selectView-timeline'){
                $(filterDiv).hide();
		view.display(timelineData);
		layout.resizeAll();
	}
}

/**
 * Get the id of the node in the XML.
 * @param d
 * @returns
 */
function getId (d) {                
	return d.nodeId ? d.nodeId : d.nodeIdRef ? d.nodeIdRef : d.parentObjId ? d.parentObjId :"";
}

/** Show the context menu for showing the HTML view when right clicking a node
 * 
 * @param data The node that was clicked
 */
function showContext (node,left,top) {
	contextNode = node;
        if(d3.select(node).datum())
        {
            var data = d3.select(node).datum();
        }else
        {
            var data = node;
        }
	if (getId(data) || htmlSectionMap[data.type]) {  // disable if the node has no ID or section header 
		$('#showHtml').removeClass('disabled');
	} else { 
		$('#showHtml').addClass('disabled');
	}
	d3.select("#contextMenu")  // Display the context menu in the right position
	.style('position','absolute')
	.style('left',left)
	.style('top',top)
	.style('display','block');
	d3.event.preventDefault();
}

/**
 * Show HTML view for a given node. 
 *  If the node has an id, find and scroll to the DOM element with that id, otherwise find the DOM element that matches 
 *  the type section header. 
 * @param data The node selected to show HTML
 */
function showHtmlByContext (node) {
	var data = null;
	if(d3.select(node).datum())
        {
            data = d3.select(node).datum();
        } else
        {
            data = node;
        }
	showProcessing();
	var waitForXslt = setInterval(function () { // wait until xslt processing is complete
		if (working == 0) { 
			clearInterval(waitForXslt);
			endProcessing();
			var nodeid = getId(data);
			if (nodeid) {
				var found = false;
				$.each(xmlDocs, function (i,entry) {
					if (typeof entry.html === 'undefined') { 
						showHtml("<div id='wrapper'><h2>Could not convert XML file to HTML. Make sure you have java.exe on your path.</h2></div>");
						found = true;
						return false;
					}
					else if ($(entry.html).find(".topLevelCategory .expandableContainer[data-stix-content-id='"+nodeid+"']").get(0) != undefined) {
						showHtml(new XMLSerializer().serializeToString($(entry.html).find('#wrapper').get(0)));
						var objRef = $(".topLevelCategory .expandableContainer[data-stix-content-id='"+nodeid+"']"); 
						objRef.find('tr').eq(0).addClass("infocus");
						objRef.get(0).scrollIntoView();
						expandSection(objRef);
						found = true;
						return false;
					} else { 
						return true;
					}
				});
				// If we get here, there was no entry in xmldocs for the given node
				if (!found) { 
					showHtml("<div id='wrapper'><h2>Could not convert XML file to HTML</h2></div>");
				}
			} else { 
				var section = htmlSectionMap[data.type];
				$.each(xmlDocs, function (i,entry) {
					if (typeof entry.html === 'undefined') { 
						showHtml("<div id='wrapper'><h2>Could not convert XML file to HTML. Make sure you have java.exe on your path.</h2></div>");
						return false;
					} else if ($(entry.html).find("h2 > a:contains('"+section+"')").get(0) != undefined) {
						showHtml(new XMLSerializer().serializeToString($(entry.html).find('#wrapper').get(0)));
						$("h2 > a:contains('"+section+"')").get(0).scrollIntoView();
						return false;
					} else { 
						return true;
					}
				});
			}
		}
	}, 200);
}


function showProcessing () { 
	
	
	$('#htmlView').empty();
	$('#htmlView').addClass('loading');
	$('#htmlView').append('<div id="loadingMessage"><h3> <img src="public/icons/spinner.gif"> Processing XML Transform</h3></div>');
	layout.open("south");
}

function endProcessing () { 
	
	$('#htmlView').removeClass('loading');
	$('#htmlView').empty();
}


/**
 * display the given HTML in the HTML view panel
 * @param html
 */
function showHtml (html) { 
	$('#htmlView').empty();
	layout.open("south");
	
	$('#htmlView').append(html);
	
	// Handlers to highlight associated tree nodes on mouseenter/mouseleave
	$(".topLevelCategory .expandableContainer[data-stix-content-id] tr:has(.expandableToggle)").on("mouseenter", function () { 
		$(this).addClass("infocus");
		highlightDuplicateNodes($(this).parents(".expandableContainer").data("stix-content-id"));
	});

	$(".topLevelCategory .expandableContainer[data-stix-content-id] tr:has(.expandableToggle)").on("mouseleave", function () {
		$(this).removeClass("infocus");
		removeHighlightedNodes();
	});
	

}


/**
 * Remove all node highlighting
 */
function removeHighlightedNodes () { 
	if ((viewType === 'selectView-tree') || (viewType === 'selectView-graph')) { 
		view.removeHighlightedNodes();
	}
}



/**
 * Highlight all nodes in the tree that match the given nodeId. 
 * 
 * @param nodeId The id of the node to highlight
 */
function highlightDuplicateNodes (nodeId) {
	if ((viewType === 'selectView-tree') || (viewType === 'selectView-graph')) { 
		view.highlightDuplicateNodes(nodeId);
	}
}

/**
 * Expand a given node in the HTML and then expand all of the nested expandable nodes (uses stix_to_html function
 * to expand nested expandables).
 * @param node
 */
function expandSection (node) { 
	node.find('.expandableToggle').click();
	expandNestedExpandables(node.get(0));
}




/**
 *  Reset the display when new XML files are loaded
 */
function reset (context) {
	$(filterDiv).hide();
	
	// If the context is 'all', reset everything because we are loading new XML files
	if (context === 'all') { 
		xmlDocs = {};
		docIndex = 0;
		$('#xmlFileList').empty();
		$('#htmlView').empty();
		layout.close("south");
	}
	// In all contexts, empty the view div
	$('#contentDiv').empty();
	$('#contextMenu li:gt(0)').remove();  // remove anything that was added to the context menu
	$('#viewControls').empty(); // remove view-specific controls
}

/**
 * Handle the selection of input file(s)
 * @param fileinput
 */
function handleFileSelect(fileinput) {
	
    var mime = require('mime');
		
    var files = fileinput.get(0).files;

    // If only one JSON file was loaded (for testing purposes only)
    if (files.length == 1 && mime.lookup(files[0].name).match('application/json')) { 
    	// remove old xml docs
    	reset('all');

        var reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = (function(theFile) {
          return function(e) {
            displayTree(e.target.result);
          };
        })(files[0]);

        // Read in the JSON file as text
        reader.readAsText(files[0]);

    } else if (files.length > 0) { // When one or more XML files are selected

    	reset('all');     	// remove old xml docs and reset display
    	
    	$(files).each(function (index, f) {
    	    
    		var mimetype = mime.lookup(f.name);

    		// Only process xml files.
    		if (!mimetype.match('application/xml')) {
    			return;
    		} else { 
        	    addXmlDoc(f);  // adds the new XML file to the drop down menu in the UI
    		}
    	});
    	
    	generateJsonForFiles(files, viewType, displayJson);

    	/*
    	if ((viewType === 'selectView-tree') || (viewType === 'selectView-graph')) { 
    		generateTreeJson(files,displayRelationshipJSON);
		} else if (viewType === 'selectView-timeline'){
    		generateTimelineJson(files,displayTimelineJSON);
		}
		*/
    }

};



/**
 * Highlight the div in the HTML view corresponding to the given nodeId
 * @param nodeId
 */
function highlightHtml (nodeId) { 
	if (!nodeId) return;
	$(".topLevelCategory .expandableContainer[data-stix-content-id='"+nodeId+"'] tr").eq(0).addClass("infocus");
}

