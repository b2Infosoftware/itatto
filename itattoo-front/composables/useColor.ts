export const useColor = () => {
  type Hex = string;
  /**
   * Calculate contrast value to white
   */
  const contrastToWhite = (hexColor: Hex) => {
    var whiteIlluminance = 1;
    var illuminance = calculateIlluminance(hexColor);
    return whiteIlluminance / illuminance;
  };

  const adjust = (hexColor: Hex, decrease: number = 110) => {
    return (
      '#' +
      hexColor
        .replace(/^#/, '') 
        .replace(/../g, (hexColor) =>
          (
            '0' +
            Math.min(255, Math.max(0, parseInt(hexColor, 16) - decrease)) 
            .toString(16)
          ).substr(-2)
        )
    );
  };

  /**
   * Bool if there is enough contrast to white
   */
  const getContrastColor = (hexColor: Hex) => {
    if (contrastToWhite(hexColor) > 4.5) {
      return 'white';
    }
    const initialContrast = contrastToWhite(hexColor);
    const decrease = initialContrast < 2 ? 250 : initialContrast < 3 ? 180 : 170;
    const newColor = adjust(hexColor, decrease);
    return newColor;
  };

  /**
   * Convert HEX color to RGB
   */
  const hex2Rgb = function (hexColor: Hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hexColor);
    return result
      ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16),
        }
      : null;
  };

  /**
   * Calculate iluminance
   */
  const calculateIlluminance = function (hexColor: Hex) {
    const rgbColor = hex2Rgb(hexColor);
    if (!rgbColor) {
      return 0;
    }
    const r = rgbColor.r,
      g = rgbColor.g,
      b = rgbColor.b;
    const a = [r, g, b].map(function (v) {
      v /= 255;
      return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
  };

  return { getContrastColor, adjust };
};
