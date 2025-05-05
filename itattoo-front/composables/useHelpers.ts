export const useHelpers = () => {
  var groupBy = function (xs, key) {
    return xs.reduce(function (rv, x) {
      (rv[x[key]] = rv[x[key]] || []).push(x);
      return rv;
    }, {});
  };

  const niceTime = (minutes: number) => {
    const hrs = Math.floor(minutes / 60);
    if (hrs > 0) {
      let niceMins = minutes % 60;
      if (niceMins < 10) {
        niceMins = '0' + niceMins;
      }

      return hrs + ':' + niceMins + ' hrs';
    }
    return minutes + ' mins';
  };
  return { groupBy, niceTime };
};
