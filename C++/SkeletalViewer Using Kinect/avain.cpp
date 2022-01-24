
	
	glBegin(GL_POLYGON);
	glTexCoord2d(0,0);
	glVertex3f(0.0,0.0,0.0);
	glTexCoord2d(0,1);
	glVertex3f(0.0,150.0,0.0);
	glTexCoord2d(1,0);
	glVertex3f(150.0,150.0,0.0);
	
	glTexCoord2d(1,1);
	glVertex3f(150.0,0.0,0.0);
	
	glVertex3f(0.0,0.0,0.0);
	glEnd();

		glBegin(GL_POLYGON);
	glVertex3f(0.0,0.0,0.0);
	glTexCoord2d(1,1);
	glVertex3f(0.0,150.0,0.0);
	glTexCoord2d(0,1);
	glVertex3f(150.0,150.0,0.0);
	glTexCoord2d(0,0);

	glVertex3f(150.0,0.0,0.0);
	glTexCoord2d(1,0);
	glVertex3f(0.0,0.0,0.0);
	glEnd();