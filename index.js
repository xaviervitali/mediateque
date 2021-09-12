function imageFormatter(img) {

  return `<img src=${img} alt="${img}" >`;
}

function dateFormatter(date) {
  return new Date(date).toLocaleDateString("FR");
}

function lastAiredFormatter(data) {
  return data.in_production
    ? `<p>Date : ${dateFormatter(data.last_air_date)}</p><p>Episode : ${
        data.last_episode_to_air.episode_number
      }</p><p>Saison : ${data.last_episode_to_air.season_number}</p>`
    : `<p>Serie arretée le  ${dateFormatter(data.last_air_date)}</p>`;
}

function watchedFormatter(data) {
  return data
    ? '<i class="fas fa-check-circle text-success"></i>'
    : '<i class="fas fa-times-circle text-danger"></i>';
}

function detailFormatter(index, row) {
  map = row.episodesList.map((e) => {
    return {
      c12: e.c12,
      c13: e.c13,
      c00: e.c00,
      c06: e.c06,
      c01: e.c01,
      c05: e.c05,
      vu: e.lastPlayed,
    };
  });

  const Synopsis =
    "<p><p style='font-size:1.2rem;font-weight:bold'>Pitch</p><p>" +
    row.pitch +
    "</p>";
  return Synopsis + buildTable(map);
}

function buildTable(epList) {
  epList.sort((a, b) => new Date(b.c05) - new Date(a.c05));

  const keys = Object.keys(epList[0]);
  const rows = epList.length;
  let body = "";
  keys.forEach((k) => {
    for (i = 0; i < rows; i++) {
      body += "<tr>";

      keys.forEach(
        (k) =>
          (body +=
            k === "c05"
              ? "<td>" + new Date(epList[i][k]).toLocaleDateString() + "</td>"
              : k === "vu"
              ? "<td>" + watchedFormatter(epList[i][k]) + "</td>"
              : k === "c06"
              ? "<td>" +
                "<img style='max-width:10rem' src=" +
                epList[i][k] +
                "></td>"
              : "<td>" + epList[i][k] + "</td>")
      );
      body += "</tr>";
    }
  });
  return (
    "<table class='table' style='font-size:.8rem'><thead><th>Saison</th><th>Episode</th><th>Titre</th><th>poster</th><th>Synopsis</th><th>Diffusé le</th><th>Vu</th></thead><tbody>" +
    body +
    "</tbody></table>"
  );
}
