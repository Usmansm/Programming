<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:xs="http://www.w3.org/2001/XMLSchema"
  xmlns:incident="http://stix.mitre.org/Incident-1"
  exclude-result-prefixes="xs"
  version="2.0">
  
  <!--
    This xslt stylesheet runs under the mode "identifyAnonymousItems", which
    means that any of the specified elements that show up in the source
    document without an id or idref, will be assigned an autogenerated id.
  -->
  
  <!--
    the default behavior for an element is to copy all of its attributes and
    its child text and element nodes.
  -->
  <xsl:template match="@*|node()" mode="identifyAnonymousItems">
    <xsl:copy>
      <xsl:apply-templates select="@*|node()" mode="#current"/>
    </xsl:copy>
  </xsl:template>
  
  <!--
    For the specified elements, also copy their attribues and child elements
    and text nodes, but if the element does not have an id or idref attribute,
    assign an autogenerated id number.
  -->
  <xsl:template match="*:Observable|*:Indicator|*:TTP|*:Exploit_Target|*:Incident|*:Course_Of_Action|*:Campaign|*:Threat_Actor[not(self::incident:Threat_Actor)]" mode="identifyAnonymousItems">
    <xsl:copy>
      <xsl:if test="not(@id) and not(@idref)">
        <xsl:variable name="newId" select="generate-id(.)" />
        <xsl:attribute name="id" select="concat('AUTO_GENERATED_ID_', $newId)" />
      </xsl:if>
      <xsl:apply-templates select="@*|node()" mode="#current"/>
    </xsl:copy>
  </xsl:template>
  
</xsl:stylesheet>