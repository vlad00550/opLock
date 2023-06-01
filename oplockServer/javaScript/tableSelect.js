// Дожидаемся полной загрузки документа
document.addEventListener("DOMContentLoaded", function() {
  // Находим ссылки на все строки таблицы
  const rows = document.querySelectorAll("tr");

  // Привязываем обработчик клика к каждой строке таблицы
  rows.forEach(function (row) {
    row.addEventListener("click", tableRowClick);
  });
});
// Обработчик клика на строке таблицы
function tableRowClick(event) {
  // Отменяем действие по умолчанию (клик по ссылке)
  event.preventDefault();

  // Получаем объект строки таблицы, на которую кликнули
  const row = event.target.closest("tr");
  // Выбираем только строки с классом "selectable"
  if (row && row.classList.contains("selectable")) {
    // Меняем фон и обводку строки
	const id = row.cells[0].textContent; // Получаем содержимое первой ячейки
	const pathname = window.location.pathname;
	window.location.href = pathname + "?selectedId=" + id;
  }
}