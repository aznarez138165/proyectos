CREATE TABLE respuestas_abiertas_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT NOT NULL,
    respuesta TEXT NOT NULL,
    alumno_id INT NOT NULL, 
    FOREIGN KEY (pregunta_id) REFERENCES preguntas_abiertas(id),
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id)
);
