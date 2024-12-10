CREATE TABLE preguntas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pregunta VARCHAR(255) NOT NULL,
    respuesta_correcta INT,
    puntuacion DECIMAL(5, 2) NOT NULL
);

CREATE TABLE respuestas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pregunta_id INT,
    respuesta VARCHAR(255),
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id)
);
