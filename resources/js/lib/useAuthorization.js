import { usePage } from "@inertiajs/vue3";

export function useAuthorization() {
  const page = usePage();

  const adminPermissions = page.props.auth?.admin?.permission_names || [];
  const adminRoles = page.props.auth?.admin?.role_names || [];

  /**
   * @return {boolean}
   */
  const isSuperAdmin = () => adminRoles.includes("superadmin");

  /**
   * @param {string|Array<string>} abilities
   * @return {boolean}
   */
  const can = (abilities) => {
    if (isSuperAdmin()) return true; 
    if (Array.isArray(abilities)) {
      return abilities.every((ability) => adminPermissions.includes(ability));
    }
    return adminPermissions.includes(abilities);
  };

  /**
   * @param {string|Array<string>} abilities
   * @return {boolean}
   */
  const canAny = (abilities) => {
    if (isSuperAdmin()) return true; 
    if (Array.isArray(abilities)) {
      return abilities.some((ability) => adminPermissions.includes(ability));
    }
    return adminPermissions.includes(abilities);
  };

  /**
   * @param {string|Array<string>} abilities
   * @return {boolean}
   */
  const cant = (abilities) => {
    return !can(abilities);
  };

  /**
   * @param {string|Array<string>} abilities
   * @return {boolean}
   */
  const cannot = (abilities) => {
    return !canAny(abilities);
  };

  /**
   * @param {string} role
   * @return {boolean}
   */
  const hasRole = (role) => {
    return adminRoles.includes(role);
  };

  /**
   * Kiểm tra nếu user không có vai trò cụ thể
   * @param {string} role
   * @return {boolean}
   */
  const doesNotHaveRole = (role) => {
    return !hasRole(role);
  };

  return {
    can,
    canAny,
    cant,
    cannot,
    hasRole,
    doesNotHaveRole,
    isSuperAdmin,
  };
}
