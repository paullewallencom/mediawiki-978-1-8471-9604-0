/*
# Skin
		#
		global $wgAllowUserSkin;
		if( $wgAllowUserSkin ) {
			$wgOut->addHTML( "<fieldset>\n<legend>\n" . wfMsg( 'skin' ) . "</legend>\n" );
			$mptitle = Title::newMainPage();
			$previewtext = wfMsg( 'skin-preview' );
			# Only show members of Skin::getSkinNames() rather than
			# $skinNames (skins is all skin names from Language.php)
			$validSkinNames = Skin::getUsableSkins();
			# Sort by UI skin name. First though need to update validSkinNames as sometimes
			# the skinkey & UI skinname differ (e.g. "standard" skinkey is "Classic" in the UI).
			foreach ( $validSkinNames as $skinkey => &$skinname ) {
				$msgName = "skinname-{$skinkey}";
				$localisedSkinName = wfMsg( $msgName );
				if ( !wfEmptyMsg( $msgName, $localisedSkinName ) )  {
					$skinname = $localisedSkinName;
				}
			}
			asort($validSkinNames);
			foreach( $validSkinNames as $skinkey => $sn ) {
				$checked = $skinkey == $this->mSkin ? ' checked="checked"' : '';
				$mplink = htmlspecialchars( $mptitle->getLocalURL( "useskin=$skinkey" ) );
				$previewlink = "(<a target='_blank' href=\"$mplink\">$previewtext</a>)";
				$extraLinks = '';
				global $wgAllowUserCss, $wgAllowUserJs;
				if( $wgAllowUserCss ) {
					$cssPage = Title::makeTitleSafe( NS_USER, $wgUser->getName().'/'.$skinkey.'.css' );
					$customCSS = $sk->makeLinkObj( $cssPage, wfMsgExt('prefs-custom-css', array() ) );
					$extraLinks .= " ($customCSS)";
				}
				if( $wgAllowUserJs ) {
					$jsPage = Title::makeTitleSafe( NS_USER, $wgUser->getName().'/'.$skinkey.'.js' );
					$customJS = $sk->makeLinkObj( $jsPage, wfMsgHtml('prefs-custom-js') );
					$extraLinks .= " ($customJS)";
				}
				if( $skinkey == $wgDefaultSkin )
					$sn .= ' (' . wfMsg( 'default' ) . ')';
				$wgOut->addHTML( "<input type='radio' name='wpSkin' id=\"wpSkin$skinkey\" value=\"$skinkey\"$checked /> 
					<label for=\"wpSkin$skinkey\">{$sn}</label> $previewlink{$extraLinks}<br />\n" );
			}
			$wgOut->addHTML( "</fieldset>\n\n" );
*/