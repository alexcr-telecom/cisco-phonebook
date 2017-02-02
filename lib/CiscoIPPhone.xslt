<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:msxsl="urn:schemas-microsoft-com:xslt" exclude-result-prefixes="msxsl">
    <xsl:output method="html" indent="yes"/>
    <xsl:template match="/">
        <xsl:for-each select="CiscoIPPhoneMenu">
	    <form name="cisco-xml-form" method="POST"><xsl:attribute name="action"><xsl:value-of select="URL"/></xsl:attribute>
              <table style="width:100%">
                <tr><td><b><xsl:value-of select="Title"/></b></td></tr>
                <xsl:variable name="hasPrompt" select="Prompt"/>
                <xsl:if test="$hasPrompt"><tr><td><small style="padding-left:20px;"><xsl:value-of select="Prompt"/></small></td></tr></xsl:if>
                <xsl:for-each select="MenuItem">
                   <tr><td><a><xsl:attribute name="href"><xsl:value-of select="URL"/></xsl:attribute><xsl:value-of select="Name"/></a></td></tr>
                </xsl:for-each>
                <xsl:variable name="hasSoftKeys" select="SoftKeyItem"/>
                <xsl:if test="$hasSoftKeys">
                  <tr>
                  <table><tr>
                  <xsl:for-each select="SoftKeyItem">
                    <td><a><xsl:attribute name="href"><xsl:value-of select="URL"/></xsl:attribute><xsl:value-of select="Name"/></a></td>
                  </xsl:for-each>
                  </tr></table>
                  </tr>
                </xsl:if>
              </table>
            </form>
        </xsl:for-each>
        <xsl:for-each select="CiscoIPPhoneText">
            <b><xsl:value-of select="Title"/></b><br/>
            <xsl:variable name="hasPrompt" select="Prompt"/>
            <xsl:if test="$hasPrompt"><small style="padding-left:20px;"><xsl:value-of select="Prompt"/></small><br/></xsl:if>
            <p><xsl:value-of select="Text"/></p>
        </xsl:for-each>
        <xsl:for-each select="CiscoIPPhoneInput">
            <script language="Javascript" src="http://code.jquery.com/jquery-latest.pack.js"></script>
            <script language="Javascript" src="http://jquery-joshbush.googlecode.com/files/jquery.maskedinput-1.2.2.js"></script>
            <script language="Javascript" type="text/javascript">
               jQuery(document).ready(function() {
                  $(".mask-phone").mask("(999) 999-9999",{placeholder:" "}) ;
                  $(".mask-number").bind('keyup', function() { $(this).val($(this).val().replace(/[^0-9]/gi, '')); });
                  $(".mask-uppercase").bind('keyup', function() { $(this).css({textTransform: "uppercase"}); });
                  $(".mask-lowercase").bind('keyup', function() { $(this).css({textTransform: "lowercase"}); });
               });
	    </script>
            <b><xsl:value-of select="Title"/></b><br/>
            <xsl:variable name="hasPrompt" select="Prompt"/>
            <xsl:if test="$hasPrompt"><small style="padding-left:20px;"><xsl:value-of select="Prompt"/></small><br/></xsl:if>
            <form name="cisco-xml-form" method="POST"><xsl:attribute name="action"><xsl:value-of select="URL"/></xsl:attribute>
                <xsl:for-each select="InputItem">
                    <xsl:variable name="inputType" select="InputFlags"/>
                    <xsl:choose>
                        <xsl:when test="$inputType = 'T'">
                            <xsl:value-of select="DisplayName"/>: <input type="text" class="mask-phone"><xsl:attribute name="name"><xsl:value-of select="QueryStringParam"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="DefaultValue"/></xsl:attribute></input><br/>
                        </xsl:when>
                        <xsl:when test="$inputType = 'N'">
                            <xsl:value-of select="DisplayName"/>: <input type="text" class="mask-number"><xsl:attribute name="name"><xsl:value-of select="QueryStringParam"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="DefaultValue"/></xsl:attribute></input><br/>
                        </xsl:when>
                        <xsl:when test="$inputType = 'U'">
                            <xsl:value-of select="DisplayName"/>: <input type="text" class="mask-uppercase"><xsl:attribute name="name"><xsl:value-of select="QueryStringParam"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="DefaultValue"/></xsl:attribute></input><br/>
                        </xsl:when>
                        <xsl:when test="$inputType = 'L'">
                            <xsl:value-of select="DisplayName"/>: <input type="text" class="mask-lowercase"><xsl:attribute name="name"><xsl:value-of select="QueryStringParam"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="DefaultValue"/></xsl:attribute></input><br/>
                        </xsl:when>
                        <xsl:when test="$inputType = 'P'">
                            <xsl:value-of select="DisplayName"/>: <input type="password"><xsl:attribute name="name"><xsl:value-of select="QueryStringParam"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="DefaultValue"/></xsl:attribute></input><br/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="DisplayName"/>: <input type="text"><xsl:attribute name="name"><xsl:value-of select="QueryStringParam"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="DefaultValue"/></xsl:attribute></input><br/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
                <input type="submit" value="Submit"/>
            </form>
        </xsl:for-each>
        <xsl:for-each select="CiscoIPPhoneDirectory">
            <table>
            <xsl:for-each select="DirectoryEntry">
                <tr>
                    <td><xsl:value-of select="Name"/></td>
                    <td><xsl:variable name="phoneNumber" select="Telephone"/><xsl:choose><xsl:when test="string-length($phoneNumber) = 7"><xsl:value-of select="concat('(204) ', substring($phoneNumber, 1, 3), '-', substring($phoneNumber, 4))"/></xsl:when><xsl:otherwise><xsl:value-of select="concat('(', substring($phoneNumber, 1, 3), ') ', substring($phoneNumber, 4, 3), '-', substring($phoneNumber, 7))"/></xsl:otherwise></xsl:choose></td>
                </tr>
            </xsl:for-each>
            </table>
        </xsl:for-each>
        <xsl:for-each select="CiscoIPPhoneImageFile">
            <img><xsl:attribute name="src"><xsl:value-of select="URL" disable-output-escaping="yes"/></xsl:attribute></img>
        </xsl:for-each>
        <xsl:for-each select="CiscoIPPhoneGraphicFileMenu">
            <img usemap="#ciscomenu"><xsl:attribute name="src"><xsl:value-of select="URL"/></xsl:attribute></img>
            <map name="ciscomenu">
            <xsl:for-each select="MenuItem">
                <xsl:variable name="areaURL"><xsl:value-of select="URL"/></xsl:variable>
                <xsl:for-each select="TouchArea">
                    <xsl:variable name="x1" select="@X1"/><xsl:variable name="y1" select="@Y1"/><xsl:variable name="x2" select="@X2"/><xsl:variable name="y2" select="@Y2"/>
                    <area shape="rect"><xsl:attribute name="href"><xsl:value-of select="$areaURL"/></xsl:attribute><xsl:attribute name="coords"><xsl:value-of select="concat($x1, ',', $y1, ',', $x2, ',', $y2)"/></xsl:attribute></area>
                </xsl:for-each>
            </xsl:for-each>
            </map>
        </xsl:for-each>
        <xsl:for-each select="CiscoIPPhoneIconFileMenu">
            <style type="text/css">
               ul {list-style-type:none;}
               <xsl:for-each select="IconItem">
               .icon-<xsl:value-of select="Index"/> {background-image:URL(<xsl:value-of select="URL"/>);background-position:2px left;background-repeat:no-repeat;padding-left:21px;}
               </xsl:for-each>
            </style>
            <b><xsl:value-of select="Title" /></b><br/>
            <xsl:variable name="hasPrompt" select="Prompt"/>
            <xsl:if test="$hasPrompt"><small style="padding-left:20px;"><xsl:value-of select="Prompt"/></small><br/></xsl:if>
            <ul>
            <xsl:for-each select="MenuItem">
                <li><a><xsl:attribute name="href"><xsl:value-of select="URL"/></xsl:attribute><xsl:attribute name="class">icon-<xsl:value-of select="IconIndex"/></xsl:attribute><xsl:value-of select="Name"/></a></li>
            </xsl:for-each>
            </ul>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
