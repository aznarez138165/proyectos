CREATE TABLE `respuestas_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta_id` int(11) NOT NULL,
  `examen_id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `respuesta_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`pregunta_id`) REFERENCES `preguntas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`examen_id`) REFERENCES `examenes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`alumno_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`respuesta_id`) REFERENCES `respuestas`(`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
