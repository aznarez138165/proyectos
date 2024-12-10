CREATE TABLE examen_pregunta (
    examen_id INT,
    pregunta_id INT,
    PRIMARY KEY (examen_id, pregunta_id),
    FOREIGN KEY (examen_id) REFERENCES examenes(id),
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id)
);
