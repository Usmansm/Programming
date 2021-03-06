<?xml version="1.0" encoding="UTF-8"?>
<!--
  Copyright (c) 2013 – The MITRE Corporation
  All rights reserved. See LICENSE.txt for complete terms.
 -->
<!--
STIX XML to HTML transform v1.0
Compatible with CybOX v2.0

This is an xslt to transform a STIX 2.0 document into html for easy viewing.  
CybOX observables, Indicators & TTPs are supported and turned into collapsible 
HTML elements.  Details about structure's contents are displayed in a
format representing the nested nature of the original document.

Objects which are referred to by reference can be expanded within the context
of the parent object, unless the reference points to an external document

This is a work in progress.  Feedback is most welcome!

requirements:
 - XSLT 2.0 engine (this has been tested with Saxon 9.5)
 - a STIX 2.0 input xml document
 
Created 2013
mcoarr@mitre.org
mdunn@mitre.org
  
-->

<xsl:stylesheet version="2.0"
  xmlns:stix="http://stix.mitre.org/stix-1"
  xmlns:cybox="http://cybox.mitre.org/cybox-2"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xmlns:fn="http://www.w3.org/2005/xpath-functions"
  xmlns:xs="http://www.w3.org/2001/XMLSchema"
  xmlns:indicator="http://stix.mitre.org/Indicator-2"
  xmlns:TTP="http://stix.mitre.org/TTP-1"
  xmlns:COA="http://stix.mitre.org/CourseOfAction-1"
  xmlns:ET="http://stix.mitre.org/ExploitTarget-1"
  xmlns:ta="http://stix.mitre.org/ThreatActor-1"
  xmlns:ttp="http://stix.mitre.org/TTP-1"
  xmlns:incident="http://stix.mitre.org/Incident-1"
  xmlns:capec="http://stix.mitre.org/extensions/AP#CAPEC2.5-1"
  xmlns:marking="http://data-marking.mitre.org/Marking-1"
  xmlns:tlpMarking="http://data-marking.mitre.org/extensions/MarkingStructure#TLP-1"
  xmlns:stixVocabs="http://stix.mitre.org/default_vocabularies-1"
  xmlns:cyboxCommon="http://cybox.mitre.org/common-2"
  xmlns:cyboxVocabs="http://cybox.mitre.org/default_vocabularies-2"
  xmlns:stixCommon="http://stix.mitre.org/common-1"
  xmlns:EmailMessageObj="http://cybox.mitre.org/objects#EmailMessageObject-2"
  exclude-result-prefixes="cybox xsi fn EmailMessageObj">

  <xsl:import href="icons.xsl"/>
  
  <xsl:output method="html" omit-xml-declaration="yes" indent="yes" media-type="text/html"
    version="4.0"/>
  
  
  <!--
    how to set parameters: xslt stylesheet parameters should be passed in via
    whatever mechanism the xslt engine has.  saxon allows you to set parameters
    via the command-line or via the java api.  oxygen and xml spy allow you to
    set parameters via the xslt configuration.
  -->  
  
  <!--
    include the file metadata header that shows stix version, filename, and html generation timestamp
  -->  
  <xsl:param name="includeFileMetadataHeader" select="true()"/>
  
  <!--
    include the stix header - the header table that shows the title, package
    intent, description, handling, information source, etc.
  -->
  <xsl:param name="includeStixHeader" select="true()"/>
  
  <!--
    set to true if you want to preserve line breaks in the description text,
    fields, otherwise text descriptions will be flowed like normal html text
  -->
  <xsl:param name="enablePreformattedDescriptions" select="false()" />
  
  <!--
    set the style of how svg images to be used for item type icons:
      * inlineLiteralXml - the svg xml will show up inline in the resulting html document
      * dataUri          - the svg image will be included in the document via the img element's src attribute using a data uri whose contents are a the base64 encoded version of the gzipped svg
      * relativeUri      - normal html image style references, e.g. <img src="images/logo.svg" /> 
  -->
  <xsl:param name="iconReferenceStyle" select="'inlineLiteralXml'" />
  <!-- base uri to where image svg files are located (only used for iconReferenceStyle == "relativeUri") -->
  <xsl:param name="iconExternalImageBaseUri" select="'images'" />
  <xsl:variable name="iconExternalImageBaseUriVariable" select="$iconExternalImageBaseUri" />
  
  <!--
    do you want to display the constraints in cyboxProperties-style displays?
    usually the answer is true(), but if you want a more concise display, set to false().
  -->
  <xsl:param name="displayConstraints" select="true()"/>

  <xsl:include href="stix_common.xsl"/>
  <xsl:include href="normalize.xsl"/>
  <xsl:include href="identify_anonymous_items.xsl" />
  
  <!-- do not modify this, this is used inside icons.xsl to pass in the value of the iconReferenceStyle parameter -->
  <xsl:variable name="iconReferenceStyleVariable" select="$iconReferenceStyle" />
  
  <xsl:variable name="isRootStix" select="fn:exists(/stix:STIX_Package)" />
  <xsl:variable name="isRootCybox" select="fn:exists(/cybox:Observables)" />

  <!--
    This prints out the header at the top of the page.
    
    This can be customized by either editing here or having another xsl
    stylesheet import stix_to_html.xsl and defining your own "customHeader"
    template.
  -->
  <xsl:template name="customHeader">
    <div class="customHeader">
      <xsl:comment>no custom header provided</xsl:comment>
    </div>
  </xsl:template>

  <!--
    This prints out the title near the top of the page.
    
    This can be customized by either editing here or having another xsl
    stylesheet import stix_to_html.xsl and defining your own "customTitle"
    template.
  -->
  <xsl:template name="customTitle">
    <div class="customTitle">
      <xsl:comment>no custom title provided</xsl:comment>
      <h1>
        <xsl:if test="$isRootStix">STIX</xsl:if>
        <xsl:if test="$isRootCybox">CYBOX</xsl:if>
        Report
      </h1>
    </div>
  </xsl:template>

  <!--
    This prints out the footer at the bottom of the page.
    
    This can be customized by either editing here or having another xsl
    stylesheet import stix_to_html.xsl and defining your own "customFooter"
    template.
  -->
  <xsl:template name="customFooter">
    <div class="customFooter">
      <xsl:comment>no custom footer provided</xsl:comment>
      <!-- &#xA9; Generic Company -->
    </div>
  </xsl:template>

  <!--
    if your organization wants to customize the css styling, override this template
    
    the easiest thing to do is to reference an external stylesheet to be included:
    
      <style type="text/css">
        <xsl:value-of select="unparsed-text('custom.css')" />
      </style>

    OR
    
    use inline styles:
    
    <style type="text/css">
    .customHeader { color: red; }
    .customFooter { color: blue; }
    </style>

  -->
  <xsl:template name="customCss"> </xsl:template>

  <xsl:template name="configurableCss">
    <style type="text/css">
    .cyboxPropertiesConstraints
    {
      <xsl:if test="not($displayConstraints)">
        display: none;
      </xsl:if>
      }
      
      .description, .StixDescriptionValue 
      {
      /* if the descriptions are "preformatted text" use "pre-line" or "pre" */
      <xsl:if test="$enablePreformattedDescriptions">
      white-space: pre-line;
      </xsl:if>
      }
      
    
    </style>
  </xsl:template>

  <!--
      This is the main template that sets up the html page that sets up the
      html structure, includes the base css and javascript, and adds the
      content for the metadata summary table up top and the heading and
      surrounding content for the Observables table.
    -->
  <xsl:template match="/">
    <!--
          Perform the normalization to create "normalized" and "reference".
          "reference" is a sequence with all elements from the source document
          that have @id attributes.
          
          "normalized" has a cleaned up view of the root of the source document
          down to the first elements with @id attributes, which will be renamed
          to @idref.
          
          These two variables will become the main inputs to the primary transform.
        -->
    <!-- REFERENCE: HELP_UPDATE_STEP_1A -->
    <xsl:message>cleaning up input...</xsl:message>
    <xsl:variable name="root" select="/" />
    <xsl:variable name="cleanedInput">
      <xsl:apply-templates select="$root" mode="cleanup" />
    </xsl:variable>
    <xsl:message>DONE cleaning up input.</xsl:message>
    
    <xsl:message>identifying input...</xsl:message>
    <xsl:variable name="identifiedInput">
      <xsl:apply-templates select="$cleanedInput" mode="identifyAnonymousItems" />
    </xsl:variable>
    <xsl:message>DONE identifying input.</xsl:message>

    <xsl:message>normalizing input...</xsl:message>
    <xsl:variable name="normalized">
      <xsl:apply-templates select="$identifiedInput/(stix:STIX_Package/*|cybox:Observables)" mode="createNormalized"/>
    </xsl:variable>
    <xsl:message>DONE normalizing input.</xsl:message>
    <xsl:message>creating reference...</xsl:message>
    <xsl:variable name="reference">
      <xsl:apply-templates
        select="$identifiedInput/(cybox:Observables//*|stix:STIX_Package//*)[@id or @phase_id[../../self::stixCommon:Kill_Chain] or self::cybox:Object or self::cybox:Event 
            or self::cybox:Related_Object or self::cybox:Associated_Object or self::cybox:Action_Reference or self::cybox:Action]"
        mode="createReference">
        <xsl:with-param name="isTopLevel" select="fn:true()"/>
        <xsl:with-param name="isRoot" select="fn:true()"/>
      </xsl:apply-templates>
    </xsl:variable>
    <xsl:message>DONE creating reference.</xsl:message>
    <xsl:message>processing main...</xsl:message>

    <html>
      <head>
        <title>
          <xsl:if test="$isRootStix">STIX</xsl:if>
          <xsl:if test="$isRootCybox">CYBOX</xsl:if>
          Report
          Output
        </title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

        <!-- read in the main css -->
        <style type="text/css">
                    <xsl:value-of select="unparsed-text('common.css')"/>
                </style>
        <!-- read in the theme css (mainly coloring) -->
        <style type="text/css">
                  <xsl:value-of select="unparsed-text('theme_default.css')"/>
                </style>

        <xsl:call-template name="customCss"/>

        <xsl:call-template name="configurableCss"/>

        <!-- this is a javascript shim to support the javascript
                   "classList" property in dom elements objects for IE.
                   
                   source: http://purl.eligrey.com/github/classList.js
                 -->
        <script type="text/javascript">
<![CDATA[
/*! @source http://purl.eligrey.com/github/classList.js/blob/master/classList.js*/
if(typeof document!=="undefined"&&!("classList" in document.createElement("a"))){(function(j){if(!("HTMLElement" in j)&&!("Element" in j)){return}var a="classList",f="prototype",m=(j.HTMLElement||j.Element)[f],b=Object,k=String[f].trim||function(){return this.replace(/^\s+|\s+$/g,"")},c=Array[f].indexOf||function(q){var p=0,o=this.length;for(;p<o;p++){if(p in this&&this[p]===q){return p}}return -1},n=function(o,p){this.name=o;this.code=DOMException[o];this.message=p},g=function(p,o){if(o===""){throw new n("SYNTAX_ERR","An invalid or illegal string was specified")}if(/\s/.test(o)){throw new n("INVALID_CHARACTER_ERR","String contains an invalid character")}return c.call(p,o)},d=function(s){var r=k.call(s.className),q=r?r.split(/\s+/):[],p=0,o=q.length;for(;p<o;p++){this.push(q[p])}this._updateClassName=function(){s.className=this.toString()}},e=d[f]=[],i=function(){return new d(this)};n[f]=Error[f];e.item=function(o){return this[o]||null};e.contains=function(o){o+="";return g(this,o)!==-1};e.add=function(){var s=arguments,r=0,p=s.length,q,o=false;do{q=s[r]+"";if(g(this,q)===-1){this.push(q);o=true}}while(++r<p);if(o){this._updateClassName()}};e.remove=function(){var t=arguments,s=0,p=t.length,r,o=false;do{r=t[s]+"";var q=g(this,r);if(q!==-1){this.splice(q,1);o=true}}while(++s<p);if(o){this._updateClassName()}};e.toggle=function(p,q){p+="";var o=this.contains(p),r=o?q!==true&&"remove":q!==false&&"add";if(r){this[r](p)}return !o};e.toString=function(){return this.join(" ")};if(b.defineProperty){var l={get:i,enumerable:true,configurable:true};try{b.defineProperty(m,a,l)}catch(h){if(h.number===-2146823252){l.enumerable=false;b.defineProperty(m,a,l)}}}else{if(b[f].__defineGetter__){m.__defineGetter__(a,i)}}}(self))};
]]>
        </script>

        <!-- read in the main javascript -->
        <script type="text/javascript">
                  <xsl:value-of select="unparsed-text('common.js')"/>
                </script>

        <!-- read in the wgxpath xpath-in-javascript library -->
        <!-- http://code.google.com/p/wicked-good-xpath/ -->
        <script type="text/javascript">
                  <xsl:value-of select="unparsed-text('wgxpath.install.js')"/>
                </script>


      </head>
      <body onload="runtimeCopyObjects(); initialize();">
        <xsl:call-template name="customHeader"/>

        <div id="wrapper">
          <xsl:if test="$includeFileMetadataHeader">
            <div id="header">
              <xsl:call-template name="customTitle"/>

              <div class="expandAll"
                onclick="expandAll(document.querySelector('.topLevelCategoryTables'));"
                  ><xsl:attribute name="id" select="'expandAll'"/>[toggle all -- all sections]</div>

              <!-- print out the stix metadata table -->
              <table class="stixMetadata hor-minimalist-a" width="100%">
                <thead>
                  <tr>
                    <th scope="col">
                      <xsl:if test="$isRootStix">STIX</xsl:if>
                      <xsl:if test="$isRootCybox">CYBOX</xsl:if>
                      Version
                    </th>
                    <th scope="col">Filename</th>
                    <th scope="col">Generation Date</th>
                  </tr>
                </thead>
                <tr>
                  <td>
                    <xsl:choose>
                      <xsl:when test="$isRootCybox">
                        <xsl:variable name="cyboxRoot" select="/cybox:Observables" />
                        <xsl:value-of select="fn:string-join(($cyboxRoot/@cybox_major_version, $cyboxRoot/@cybox_minor_version, $cyboxRoot/@cybox_update_version), '.')" />
                      </xsl:when>
                      <xsl:otherwise>
                        <xsl:value-of select="//stix:STIX_Package/@version"/>
                      </xsl:otherwise>
                    </xsl:choose>
                  </td>
                  <td>
                    <xsl:value-of select="tokenize(document-uri(.), '/')[last()]"/>
                  </td>
                  <td>
                    <xsl:value-of select="current-dateTime()"/>
                  </td>
                </tr>
              </table>
            </div>
            <!-- TODO: Toggle this in customization settings -->
            <h2>
              <a name="docContents">Document Contents</a>
            </h2>
            <div class="documentContentsList">
              <a href="#observablesTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:Observables|//cybox:Observables">
                    <xsl:call-template name="iconObservables"/>
                  </xsl:if>
                </div>
              </a>
              <a href="#indicatorsTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:Indicators">
                    <xsl:call-template name="iconIndicators"/>
                  </xsl:if>
                </div>
              </a>
              <a href="#ttpsTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:TTPs">
                    <xsl:call-template name="iconTTPs"/>
                  </xsl:if>
                </div>
              </a>
              <a href="#exploitTargetsTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:Exploit_Targets">
                    <xsl:call-template name="iconExploitTargets"/>
                  </xsl:if>
                </div>
              </a>
              <a href="#incidentsTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:Incidents">
                    <xsl:call-template name="iconIncidents"/>
                  </xsl:if>
                </div>
              </a>
              <a href="#coursesOfActionTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:Courses_Of_Action">
                    <xsl:call-template name="iconCOAs"/>
                  </xsl:if>
                </div>
              </a>
              <a href="#campaignsTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:Campaigns">
                    <xsl:call-template name="iconCampaigns"/>
                  </xsl:if>
                </div>
              </a>
              <a href="#threatActorsTopLevelCategoryContainer">
                <div class="documentContentsItem">
                  <xsl:if test="//stix:Threat_Actors">
                    <xsl:call-template name="iconThreatActors"/>
                  </xsl:if>
                </div>
              </a>
              
              <!-- no links to "marking" yet -->
              <div class="documentContentsItem">
                <xsl:if test="//marking:Marking">
                  <xsl:call-template name="iconDataMarkings"/>
                </xsl:if>
              </div>
              
            </div> <!-- end of div class="documentContentsList" -->

          </xsl:if>
          <xsl:if test="$includeStixHeader and $isRootStix">
            <h2>
              <a name="analysis">
                <xsl:if test="$isRootStix">STIX</xsl:if>
                <xsl:if test="$isRootCybox">CYBOX</xsl:if>
                Header
              </a>
            </h2>
            <xsl:call-template name="processHeader"/>
          </xsl:if>

          <!--
            IMPORTANT
            
            Transform and print out the "reference" objects
            
            these objects will be used any time the user clicks
            on expandable content that uses ids and idrefs.
            
            When the user expands content, the appropriate nodes
            from here will be cloned and copied into the document
          -->

          <!-- REFERENCE: HELP_UPDATE_STEP_1C -->
          <xsl:call-template name="printReference">
            <xsl:with-param name="reference" select="$reference" tunnel="yes" />
            <xsl:with-param name="normalized" select="$normalized" />
          </xsl:call-template>
          
          <xsl:call-template name="processAllTopLevelTables">
            <xsl:with-param name="reference" select="$reference" tunnel="yes" />
            <xsl:with-param name="normalized" select="$normalized" tunnel="yes" />
          </xsl:call-template>


        </div>

        <xsl:call-template name="customFooter"/>
      </body>
    </html>
    <xsl:message>DONE processing main.</xsl:message>
    <xsl:message>##########</xsl:message>
  </xsl:template>
  
  <!--
    This template prints out the "reference" variable that comes of the the
    "normalization" transform.
    
    The contents are transformed into html and printed out as html elements
    with corresponding ids.
    
    This is contained in a div with class .reference which will be hidden.
    Its contents are never directly visible to the user.
    
    The following templates with mode "printReference" are used in transforming
    and building this content.
  -->
  <xsl:template name="printReference">
    <xsl:param name="reference" select="()" tunnel="yes" />
    <xsl:param name="normalized" select="()" tunnel="yes" />

    <div class="reference">
      <xsl:apply-templates select="$reference" mode="printReference"/>
    </div>
  </xsl:template>

  <!--
    For printing reference objects, default to not printing anything.
    Another template must apply if an element or attribute should be printed
    out.
  -->
  <xsl:template match="node()" mode="printReference"/>

  <!--
    Opt in the following nodes to being printed in reference list:
     - Observable
     - Indicator
     - TTP
     - Kill Chain
     - Campaign
     - Incident
     - Thread Actor
     - Exploit Target
  -->
  <!-- REFERENCE: HELP_UPDATE_STEP_1D -->
  <xsl:template
    match="cybox:Observable|stixCommon:Observable|indicator:Observable|stix:Indicator|stixCommon:Indicator|indicator:Indicator|stix:TTP|stixCommon:TTP|stixCommon:Kill_Chain_Phase|stix:Campaign|stixCommon:Campaign|stix:Incident|stixCommon:Incident|stix:Threat_Actor|stixCommon:Threat_Actor|ET:Exploit_Target|stixCommon:Exploit_Target|stixCommon:Course_Of_Action|stix:Course_Of_Action|TTP:Identity|marking:Marking|stixCommon:Identity|ta:Identity|incident:Victim|ttp:Attack_Pattern"
    mode="printReference">
    <xsl:param name="reference" select="()"/>
    <xsl:param name="normalized" select="()"/>

    <xsl:call-template name="printGenericItemForReferenceList">
      <xsl:with-param name="reference" select="$reference"/>
      <xsl:with-param name="normalized" select="$normalized"/>
    </xsl:call-template>
  </xsl:template>

  <!--
    Opt in the following nodes to being printed in reference list:
     - Object
     - Related Object
     - Kill Chain
     - Course Of Action
  -->
  <!-- REFERENCE: HELP_UPDATE_STEP_1D -->
  <xsl:template
    match="cybox:Object|cybox:Event|cybox:Associated_Object|cybox:Related_Object|stixCommon:Kill_Chain|cybox:Action"
    mode="printReference">
    <xsl:param name="reference" select="()"/>
    <xsl:param name="normalized" select="()"/>

    <xsl:call-template name="printObjectForReferenceList">
      <xsl:with-param name="reference" select="$reference"/>
      <xsl:with-param name="normalized" select="$normalized"/>
    </xsl:call-template>
  </xsl:template>

  <!--
    Print one of the "items" (Obserbale, Indicator, TTP, etc) for the "reference" list.
    
    This will always have the immediate contents of the "item".
    
    [This is related to printGenericItemForTopLevelCategoryTable, which prints
    the generic items for the top level tables.  It should be noted that that
    template never prints contents, as they will be looked up by id from the
    reference list, as printed by this template.]
  -->
  <!-- REFERENCE: HELP_UPDATE_STEP_1E -->
  <xsl:template name="printGenericItemForReferenceList">
    <xsl:param name="reference" select="()"/>
    <xsl:param name="normalized" select="()"/>

    <xsl:variable name="originalItem" select="."/>
    <xsl:variable name="originalItemId" as="xs:string?" select="fn:data($originalItem/@id)"/>
    <xsl:variable name="originalItemIdref" as="xs:string?" select="fn:data($originalItem/@idref)"/>
    <!--
    <xsl:message>
      original item id: <xsl:value-of select="$originalItemId"/>; original item idref: <xsl:value-of select="$originalItemIdref"/>; 
    </xsl:message>
    -->
    <xsl:variable name="actualItem" as="element()?"
      select="if ($originalItemId) then ($originalItem) else ($reference/*[@id = $originalItemIdref])"/>

    <xsl:variable name="expandedContentId" select="generate-id(.)"/>

    <xsl:variable name="id" select="fn:data($actualItem/@id)"/>

    <xsl:choose>
      <xsl:when test="fn:empty($actualItem)">
        <div id="{fn:data($actualItem/@id)}" class="nonExpandable">
          <div class="externalReference objectReference">
            <xsl:value-of select="$actualItem/@id"/> [EXTERNAL]
            <!--
            <xsl:call-template name="itemHeadingOnly">
              <xsl:with-param name="reference" select="$reference" />
              <xsl:with-param name="normalized" select="$normalized" />
            </xsl:call-template>
            -->
          </div>
        </div>

      </xsl:when>
      <xsl:otherwise>
        <div id="{$id}" class="expandableContainer expandableSeparate collapsed"
          data-stix-content-id="{$id}">
          <!-- <div class="expandableToggle objectReference" onclick="toggle(this.parentNode)"> -->
          <div class="expandableToggle objectReference">
            <xsl:attribute name="onclick">embedObject(this.parentElement, '<xsl:value-of
                select="$id"/>','<xsl:value-of select="$expandedContentId"/>');</xsl:attribute>
            <xsl:value-of select="$actualItem/@id"/>
            <xsl:call-template name="itemHeadingOnly">
              <xsl:with-param name="reference" select="$reference"/>
              <xsl:with-param name="normalized" select="$normalized"/>
            </xsl:call-template>

          </div>

          <div id="{$expandedContentId}" class="expandableContents">
            <!-- <div>THIS ONE</div> -->
            <xsl:choose>
              <xsl:when test="self::cybox:Observable|self::stixCommon:Observable|self::indicator:Observable">
                <div class="containerObservable">
                  <xsl:call-template name="processObservableContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::cybox:Event">
                <!-- <div>ACTION DETAILS HERE...</div> -->
                <div>
                  <div class="containerEvent">
                    <xsl:apply-templates select="."/>
                  </div>
                </div>
                <!-- <xsl:call-template name="processObservableContents" /> -->
              </xsl:when>
              <xsl:when test="self::stix:Indicator|self::stixCommon:Indicator|self::indicator:Indicator">
                <div class="containerIndicator">
                  <xsl:call-template name="processIndicatorContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::stix:TTP|self::stixCommon:TTP">
                <div class="containerTtp">
                  <xsl:call-template name="processTTPContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::TTP:Identity|self::stixCommon:Identity">
                <div class="containerIdentity">
                  <xsl:apply-templates select="*" mode="cyboxProperties"/>
                </div>
              </xsl:when>
              <xsl:when test="self::stixCommon:Kill_Chain_Phase">
                <div class="containerKillChainPhase">
                  <xsl:apply-templates select="."/>
                </div>
              </xsl:when>
              <xsl:when test="self::stix:Campaign|self::stixCommon:Campaign">
                <div class="containerCampaign">
                  <xsl:call-template name="processCampaignContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::stix:Incident|self::stixCommon:Incident">
                <div class="containerIncident">
                  <xsl:call-template name="processIncidentContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::stix:Threat_Actor|self::stixCommon:Threat_Actor">
                <div class="containerThreatActor">
                  <xsl:call-template name="processThreatActorContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::ET:Exploit_Target|self::stixCommon:Exploit_Target">
                <div class="containerExploitTarget">
                  <xsl:call-template name="processExploitTargetContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::stix:Course_Of_Action|self::stixCommon:Course_Of_Action">
                <div class="containerCourseOfAction">
                  <xsl:call-template name="processCOAContents"/>
                </div>
              </xsl:when>
              <xsl:when test="self::marking:Marking">
                <div class="containerMarking">
                    <xsl:apply-templates select="." />
                </div>
              </xsl:when>
              <xsl:when test="self::ta:Identity|stixCommon:Identity">
                <div class="containerIdentity">
                  <xsl:apply-templates select="*" />
                </div>
              </xsl:when>
              <xsl:when test="self::incident:Victim">
                <div class="containerVictim">
                  <xsl:apply-templates select="." />
                </div>
              </xsl:when>
              <xsl:when test="self::ttp:Attack_Pattern">
                <div class="containerAttackPattern">
                  <xsl:apply-templates select="." />
                </div>
              </xsl:when>
            </xsl:choose>
          </div>
        </div>
      </xsl:otherwise>
    </xsl:choose>

  </xsl:template>


</xsl:stylesheet>
