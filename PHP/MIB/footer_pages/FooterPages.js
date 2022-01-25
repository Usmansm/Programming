function showPrivacyContent(ContentID){
	nameOfContentID = "#PrivacyContent" + ContentID;
	
	if ( $(nameOfContentID).css("display") == "none") {
		$(nameOfContentID).show(700);
	}else{
		$(nameOfContentID).hide(700);
	}
	//alert("#PrivacyContent" + ContentID);
	//$(nameOfContentID).show(700);
}

function ReadMoree1(hideOrShow){
	 if (hideOrShow == 1){
		$(ReadMore1).hide(700);
		$(ReadLess1).show(700);
		$(OurPhilosophy2).show(700);
	 }
	 
	 if (hideOrShow == 2){
		$(ReadMore1).show(700);
		$(ReadLess1).hide(700);
		$(OurPhilosophy2).hide(700);
	 }
}

function ReadMoree2(hideOrShow2){
	 if (hideOrShow2 == 1){
		$(ReadMore2).hide(700);
		$(ReadLess2).show(700);
		$(TipoftheIceberg2).show(700);
	 }
	 
	 if (hideOrShow2 == 2){
		$(ReadMore2).show(700);
		$(ReadLess2).hide(700);
		$(TipoftheIceberg2).hide(700);
	 }
}

function ReadMoree3(hideOrShow3){
	 if (hideOrShow3 == 1){
		$(ReadMore3).hide(700);
		$(ReadLess3).show(700);
		$(Privacy2).show(700);
	 }
	 
	 if (hideOrShow3 == 2){
		$(ReadMore3).show(700);
		$(ReadLess3).hide(700);
		$(Privacy2).hide(700);
	 }
}

function showIt(elID) {
    var el = document.getElementById(elID);
    el.scrollIntoView(true);
}