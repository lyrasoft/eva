export function mergeRecursive(obj1: Record<string, any>, obj2: Record<string, any>, ignoreValues = [null, undefined, '']) {
  for (let p in obj2) {
    try {
      if (ignoreValues.includes(obj2[p])) {
        continue;
      }

      // Property in destination object set; update its value.
      if (obj2[p].constructor === Object) {
        obj1[p] = mergeRecursive(obj1[p], obj2[p]);
      } else {
        obj1[p] = obj2[p];
      }
    } catch (e) {
      // Property in destination object not set; create it and set its value.
      obj1[p] = obj2[p];
    }
  }
  return obj1;
}

