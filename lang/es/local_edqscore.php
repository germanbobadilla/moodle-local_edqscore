<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for local_edqscore (Spanish).
 *
 * @package    local_edqscore
 * @copyright  2026 German Bobadilla, MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Puntuación EdQ';
$string['edqscore:view'] = 'Ver las analíticas docentes de Puntuación EdQ';
$string['edqscore:manage'] = 'Gestionar la configuración predeterminada de Puntuación EdQ a nivel de sitio';
$string['edqscore:configurecourse'] = 'Anular los umbrales de tiempo de calificación de Puntuación EdQ para un curso';

$string['scopeall'] = 'Mostrando todos los estudiantes';
$string['scopegroups'] = 'Mostrando los estudiantes de tu(s) grupo(s): {$a}';
$string['scopenone'] = 'No perteneces a ningún grupo en este curso, por lo que no hay estudiantes en tu ámbito. Pide a un administrador que te añada a un grupo.';

$string['students'] = 'Estudiantes';
$string['noneinscope'] = 'No hay estudiantes en tu ámbito';
$string['nosubmittedwork'] = 'Aún no hay trabajos entregados.';
$string['selectgroup'] = 'Seleccionar grupo';
$string['allgroups'] = 'Todos los grupos';
$string['go'] = 'Ir';
$string['kpiactivities'] = 'Actividades registradas';
$string['kpioverdue'] = 'Calificaciones atrasadas';

$string['sectionassignments'] = 'Tareas';
$string['sectionforums'] = 'Foros';
$string['sectionquizzes'] = 'Cuestionarios';

$string['colname'] = 'Nombre';
$string['colstudent'] = 'Estudiante';
$string['coldue'] = 'Fecha límite';
$string['colsubmissiondate'] = 'Fecha de entrega';
$string['colgradedon'] = 'Calificado el';
$string['colgrade'] = 'Calificación';
$string['countsubmitted'] = 'Entregado';
$string['countgraded'] = 'Calificado';
$string['coloverdue'] = 'Calificación atrasada';
$string['overdueyes'] = 'Atrasado';
$string['overdueno'] = 'A tiempo';
$string['statuslate'] = 'Tarde';
$string['allcaughtup'] = 'Todo al día';
$string['coloverridden'] = 'Anulado';
$string['overriddenyes'] = 'Sí';
$string['overriddenno'] = 'No';
$string['colfeedback'] = 'Retroalimentación';
$string['feedbackshow'] = 'Mostrar';
$string['feedbackno'] = 'No';
$string['edq'] = 'EdQ';
$string['edq_help'] = 'EdQ (Calidad Educativa) es tu puntuación de tiempo de calificación: la proporción de entregas de estudiantes, respuestas en foros y intentos de cuestionario calificados manualmente que gestionaste dentro del plazo de calificación. El trabajo tardío aún cuenta con crédito parcial; solo el trabajo que ha quedado sin atender más allá del umbral de atraso reduce la puntuación.';
$string['edqnodata'] = 'Aún sin datos';
$string['edqnodatadesc'] = 'Todavía no ha ocurrido nada registrable en este curso. Cuando los estudiantes entreguen trabajos, la puntuación EdQ reflejará la rapidez con la que se califican.';
$string['edqmisses'] = 'Requiere tu atención';
$string['edqmissesall100'] = 'Todo está al día: ahora mismo nada está reduciendo la puntuación.';
$string['edqmiss_notgraded'] = 'Aún no calificado';
$string['edqmiss_gradedlate'] = 'Calificado tarde';
$string['edqmiss_nofeedback'] = 'Sin retroalimentación';
$string['edqmiss_noreply'] = 'Aún sin respuesta del docente';
$string['chartcompletiontitle'] = 'Tasa de finalización por módulo';
$string['viewdetails'] = 'Ver detalles';
$string['coldiscussions'] = 'Debates';
$string['coldiscussionname'] = 'Debate';
$string['colposts'] = 'Mensajes';
$string['collastpost'] = 'Último mensaje de';
$string['colparticipation'] = 'Participación';
$string['colinstructorreplies'] = 'Respuestas del docente';
$string['colwhen'] = 'Última respuesta';
$string['nodiscussions'] = 'Aún no hay debates.';
$string['noposts'] = 'Aún no hay mensajes';
$string['instructor'] = 'Docente';
$string['colattempts'] = 'Intentos';
$string['collastattempt'] = 'Último intento';
$string['colscore'] = 'Puntuación';
$string['colcompleted'] = 'Completado';
$string['colavgscore'] = 'Puntuación media';
$string['noactivities'] = 'Aún no hay {$a} en este curso.';

$string['autograded'] = 'Autocalificado';
$string['containsessay'] = 'Contiene ensayo';

$string['settingsheading'] = 'Valores predeterminados de tiempo de calificación';
$string['settingsheading_desc'] = 'Marca una entrega o mensaje en cuanto lleve esperando más de este tiempo, a nivel de todo el sitio: "A tiempo" hasta este umbral, "Tarde" a partir de él (consulta los ajustes de "se considera atrasado" más abajo para saber cuándo pasa a "Atrasado"). Cada curso puede anular estos valores predeterminados.';
$string['assigngradinghours'] = 'Horas de calificación de tareas';
$string['assigngradinghours_desc'] = 'Horas que puede pasar una entrega de tarea sin calificar antes de dejar de contar como "A tiempo".';
$string['forumgradinghours'] = 'Horas de calificación de foros';
$string['forumgradinghours_desc'] = 'Horas que puede pasar un mensaje de foro calificado sin calificar —o, en foros no calificados, un mensaje de un estudiante sin respuesta del docente— antes de dejar de contar como "A tiempo".';
$string['assignlatehours'] = 'Una tarea se considera atrasada después de';
$string['assignlatehours_desc'] = 'Horas por encima del umbral de calificación anterior antes de que una tarea pase de "Tarde" a "Atrasada". Se aplica incluso a trabajos ya calificados: calificarlo no lo limita a "Tarde" si ha tardado tanto.';
$string['forumlatehours'] = 'Un foro se considera atrasado después de';
$string['forumlatehours_desc'] = 'Horas por encima del umbral de calificación anterior antes de que un elemento de foro pase de "Tarde" a "Atrasado". Se aplica incluso a elementos ya calificados o respondidos: hacerlo no lo limita a "Tarde" si ha tardado tanto.';
$string['quizlatehours'] = 'Un cuestionario se considera atrasado después de';
$string['quizlatehours_desc'] = 'Horas por encima del umbral de calificación anterior antes de que un cuestionario con preguntas de ensayo pase de "Tarde" a "Atrasado". Se aplica incluso a intentos ya calificados: calificarlo no lo limita a "Tarde" si ha tardado tanto.';

$string['showungraded'] = 'Mostrar elementos sin calificar';
$string['showungraded_desc'] = 'Los elementos sin ninguna calificación configurada (por ejemplo, un foro de debate general, o una tarea/cuestionario sin calificación) se ocultan del panel de forma predeterminada, para que los docentes solo vean lo que realmente necesita calificación. Actívalo para mostrarlos también.';

$string['edqincludefeedback'] = 'Incluir la retroalimentación de tareas en la puntuación EdQ';
$string['edqincludefeedback_desc'] = 'Cuenta si se dejó texto de retroalimentación en cada tarea entregada como un par independiente de registrable/a tiempo dentro de la puntuación EdQ, junto con el tiempo de calificación. Solo se cuentan los trabajos entregados: a un estudiante que nunca entregó no se le penaliza por falta de retroalimentación.';
$string['edqincludequizmanual'] = 'Incluir cuestionarios calificados manualmente en la puntuación EdQ';
$string['edqincludequizmanual_desc'] = 'Cuenta el tiempo de calificación de los cuestionarios que contienen una pregunta de ensayo dentro de la puntuación EdQ. Los cuestionarios totalmente autocalificados nunca cuentan para la EdQ, sea cual sea este ajuste, ya que no hay ninguna acción de calificación manual que medir.';
$string['edqincludefeedbackon'] = 'Incluir retroalimentación de tareas';
$string['edqincludefeedbackoff'] = 'Excluir retroalimentación de tareas';
$string['edqincludequizmanualon'] = 'Incluir cuestionarios calificados manualmente';
$string['edqincludequizmanualoff'] = 'Excluir cuestionarios calificados manualmente';
$string['edqincludefeedback_opt_on_desc'] = 'Cuenta si se dejó retroalimentación en cada tarea entregada como parte de la puntuación EdQ.';
$string['edqincludefeedback_opt_off_desc'] = 'Deja la retroalimentación de tareas completamente fuera de la puntuación EdQ.';
$string['edqincludequizmanual_opt_on_desc'] = 'Cuenta el tiempo de calificación de cuestionarios con preguntas de ensayo dentro de la puntuación EdQ.';
$string['edqincludequizmanual_opt_off_desc'] = 'Deja el tiempo de calificación de cuestionarios completamente fuera de la puntuación EdQ.';

$string['countfrom_submission'] = 'Fecha de entrega';
$string['countfrom_duedate'] = 'Fecha límite';
$string['countfrom_duedatefallback'] = 'Fecha límite (fecha de entrega si no hay fecha límite establecida)';
$string['countfrom_cutoffdate'] = 'Fecha de cierre';
$string['countfrom_cutoffdatefallback'] = 'Fecha de cierre (fecha límite si no hay fecha de cierre establecida)';
$string['countfrom_submission_desc'] = 'El cronómetro de tiempo de calificación comienza cuando el estudiante realmente entregó o publicó.';
$string['countfrom_duedate_desc'] = 'El cronómetro comienza en la fecha límite, independientemente de cuándo el estudiante realmente entregó o publicó.';
$string['countfrom_duedatefallback_desc'] = 'Usa la fecha límite si hay una establecida; si no, recurre a la fecha de entrega o publicación.';
$string['countfrom_cutoffdate_desc'] = 'El cronómetro comienza en la fecha de cierre de la tarea —el plazo máximo tras el cual Moodle bloquea más entregas— independientemente de cuándo el estudiante realmente entregó. Solo para tareas; si no hay fecha de cierre establecida, el cronómetro nunca comienza.';
$string['countfrom_cutoffdatefallback_desc'] = 'Usa la fecha de cierre de la tarea si hay una establecida; si no, recurre a la fecha límite. Solo para tareas.';
$string['assigncountfrom'] = 'Contar el tiempo de tareas desde';
$string['assigncountfrom_desc'] = 'Desde qué momento cuenta el cronómetro de tiempo de calificación de tareas: cuándo cada estudiante realmente entregó, la fecha límite de la tarea, o su fecha de cierre (el plazo máximo tras el cual ya no se aceptan más entregas), independientemente de cuándo entregaron.';
$string['forumcountfrom'] = 'Contar el tiempo de foros desde';
$string['forumcountfrom_desc'] = 'Desde qué momento cuenta el cronómetro de calificación/respuesta de foros: cuándo el estudiante realmente publicó, o la fecha límite del foro independientemente de cuándo publicó.';
$string['quizgradinghours'] = 'Horas de calificación de cuestionarios';
$string['quizgradinghours_desc'] = 'Horas que puede pasar un intento de cuestionario con preguntas de ensayo sin calificar antes de dejar de contar como "A tiempo". Solo se aplica a cuestionarios con al menos una pregunta de ensayo: los cuestionarios totalmente autocalificados se puntúan al instante al entregarse, por lo que esto nunca se les aplica.';
$string['quizcountfrom'] = 'Contar el tiempo de cuestionarios desde';
$string['quizcountfrom_desc'] = 'Desde qué momento cuenta el cronómetro de calificación de ensayos: cuándo el estudiante entregó su intento, o la fecha de cierre del cuestionario independientemente de cuándo lo entregó. Solo se aplica a cuestionarios con preguntas de ensayo.';
$string['quizonlymanual'] = 'Mostrar solo cuestionarios calificados manualmente';
$string['quizonlymanual_desc'] = 'Los cuestionarios totalmente autocalificados se puntúan al instante en que el estudiante los entrega: no hay nada que el docente deba hacer. Activado de forma predeterminada para que el panel solo muestre los cuestionarios que realmente requieren la atención del docente (es decir, que contienen al menos una pregunta de ensayo). Desactívalo para incluir también los cuestionarios autocalificados, a modo de referencia.';
$string['quizonlymanualon'] = 'Mostrar solo cuestionarios calificados manualmente';
$string['quizonlymanualoff'] = 'Mostrar todos los cuestionarios, incluidos los totalmente autocalificados';
$string['quizonlymanual_opt_on_desc'] = 'Oculta del panel los cuestionarios totalmente autocalificados; solo muestra los que tienen una pregunta de ensayo.';
$string['quizonlymanual_opt_off_desc'] = 'Muestra todos los cuestionarios, incluidos los totalmente autocalificados, a modo de referencia.';

$string['onlyshowsubmitted'] = 'Mostrar solo trabajos entregados';
$string['onlyshowsubmitted_desc'] = 'Oculta de la lista detallada a los estudiantes que no han entregado una tarea (se siguen contando en los totales, solo no aparecen como fila). Solo tiene efecto cuando las tareas se registran por fecha de entrega; cuando se registran por fecha límite, ver quién no ha entregado todavía es precisamente el objetivo, así que esto se ignora (tu elección aquí se conserva y vuelve a aplicarse si cambias de nuevo).';
$string['onlyshowsubmittedon'] = 'Mostrar solo trabajos entregados';
$string['onlyshowsubmittedoff'] = 'Mostrar a todos, incluidos los no entregados';
$string['onlyshowsubmitted_disabled'] = 'No se aplica actualmente: las tareas de este curso se registran por fecha límite, por lo que los trabajos no entregados siempre se muestran, sea cual sea este ajuste.';
$string['onlyshowsubmitted_opt_on_desc'] = 'Oculta de la lista detallada a los estudiantes que no han entregado.';
$string['onlyshowsubmitted_opt_off_desc'] = 'Muestra a todos los estudiantes en tu ámbito, incluidos los que no han entregado.';
$string['showungradedon'] = 'Mostrar elementos sin calificar';
$string['showungradedoff'] = 'Ocultar elementos sin calificar';
$string['showungraded_opt_on_desc'] = 'Los elementos sin calificación configurada también se muestran en el panel.';
$string['showungraded_opt_off_desc'] = 'Los elementos sin calificación configurada (por ejemplo, un foro de debate general) se ocultan, para que los docentes solo vean lo que necesita calificación.';
$string['usesitedefault'] = 'Usar el valor predeterminado del sitio (actualmente: {$a})';
$string['usesitedefault_desc'] = 'Sigue el valor que tenga en cada momento el ajuste a nivel de sitio; si este cambia más adelante, este curso lo seguirá automáticamente.';
$string['shown'] = 'Mostrado';
$string['hidden'] = 'Oculto';

$string['settingsgroupgeneral'] = 'General';
$string['settingsgroupassignments'] = 'Tareas';
$string['settingsgroupforums'] = 'Foros';
$string['settingsgroupquizzes'] = 'Cuestionarios';
$string['settingsgroupnotifications'] = 'Notificaciones';
$string['settingsgroupnotifications_desc'] = 'Notificaciones de resumen diario, enviadas a través del propio sistema de notificaciones de Moodle (ventana emergente web de forma predeterminada; cada docente también puede activar el correo electrónico en sus preferencias de notificación). Desactiva cualquiera de los resúmenes por completo aquí, a nivel de sitio o por curso más abajo.';

$string['edqscoredigestenabled'] = 'Enviar resumen de puntuación EdQ';
$string['edqscoredigestenabled_desc'] = 'Una vez al día, notifica a cada docente su puntuación EdQ actual y los principales elementos pendientes de cada curso que puede ver en el panel.';
$string['edqscoredigestenabled_opt_on_desc'] = 'Enviar el resumen diario de puntuación EdQ para este curso.';
$string['edqscoredigestenabled_opt_off_desc'] = 'No enviar nunca el resumen de puntuación EdQ para este curso, sea cual sea el valor predeterminado del sitio.';

$string['submissiondigestenabled'] = 'Enviar resumen de entregas de tareas';
$string['submissiondigestenabled_desc'] = 'Una vez al día, notifica a cada docente qué estudiantes suyos entregaron una tarea en las últimas 24 horas.';
$string['submissiondigestenabled_opt_on_desc'] = 'Enviar el resumen diario de entregas para este curso.';
$string['submissiondigestenabled_opt_off_desc'] = 'No enviar nunca el resumen de entregas para este curso, sea cual sea el valor predeterminado del sitio.';

$string['digestenabledon'] = 'Activado';
$string['digestenabledoff'] = 'Desactivado';

$string['coursesettings'] = 'Configuración del curso';
$string['coursesettingsintro'] = 'Anula los umbrales de tiempo de calificación a nivel de sitio solo para este curso. Deja un campo en blanco para usar el valor predeterminado.';
$string['expandall'] = 'Expandir todo';
$string['collapseall'] = 'Contraer todo';
$string['usedefault'] = 'Predeterminado: {$a}h';
$string['savechanges'] = 'Guardar cambios';
$string['settingssaved'] = 'Configuración del curso guardada.';

$string['privacy:metadata'] = 'Puntuación EdQ no almacena datos personales; solo informa sobre datos de actividad de Moodle (entregas, mensajes, intentos de cuestionario) que ya almacenan otros complementos de Moodle, además de una configuración de tiempo de calificación por curso.';

$string['messageprovider:edqscoredigest'] = 'Resumen de puntuación EdQ';
$string['messageprovider:submissiondigest'] = 'Notificación de las entregas de tareas de tus estudiantes';

$string['task_edqscoredigest'] = 'Enviar notificaciones de resumen de puntuación EdQ';
$string['task_submissiondigest'] = 'Enviar notificaciones de resumen de entregas de tareas';

$string['digestedqscore_subject'] = 'Tu puntuación EdQ para {$a->course}: {$a->score}%';
$string['digestedqscore_small'] = 'Tu puntuación EdQ para {$a->course} es {$a->score}% ({$a->misscount} elementos requieren atención).';
$string['digestedqscore_intro'] = 'Tu puntuación EdQ para {$a->course} es {$a->score}%. {$a->misscount} elemento(s) la están reduciendo:';
$string['digestedqscore_missline'] = '{$a->student} — {$a->item} ({$a->reason})';

$string['digestsubmissions_subject'] = '{$a->count} entrega(s) de tarea nueva(s) en {$a->course}';
$string['digestsubmissions_intro'] = '{$a->count} estudiante(s) entregaron una tarea en {$a->course} en el último día:';
$string['digestsubmissions_line'] = '{$a->student} — {$a->assignment} ({$a->time})';

$string['digest_more'] = '+ {$a} más';
