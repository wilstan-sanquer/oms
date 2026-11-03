// Rangées qui défilent horizontalement (.defilement) avec des flèches précédent / suivant.
// Les flèches n'apparaissent que s'il y a plus d'éléments que la largeur de l'écran.
document.querySelectorAll('.defilement').forEach((bloc) => {
  const piste = bloc.querySelector('.defilement-piste');
  const precedent = bloc.querySelector('.defilement-precedent');
  const suivant = bloc.querySelector('.defilement-suivant');

  const majFleches = () => {
    const max = piste.scrollWidth - piste.clientWidth;
    precedent.hidden = piste.scrollLeft <= 4;
    suivant.hidden = piste.scrollLeft >= max - 4;
  };

  const defiler = (sens) => piste.scrollBy({ left: sens * piste.clientWidth * 0.8, behavior: 'smooth' });
  precedent.addEventListener('click', () => defiler(-1));
  suivant.addEventListener('click', () => defiler(1));
  piste.addEventListener('scroll', majFleches, { passive: true });
  window.addEventListener('resize', majFleches);
  window.addEventListener('load', majFleches);
  majFleches();
});
