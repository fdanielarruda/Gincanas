import {
    UserIcon,
    HomeIcon,
    ArrowLeftEndOnRectangleIcon,
    UsersIcon,
    ShieldCheckIcon,
    TrophyIcon,
    ChartBarIcon,
} from '@heroicons/vue/24/solid';

interface NavigationLink {
    label?: string;
    icon?: any;
    route?: string;
    condition?: boolean | ((isRoot: boolean) => boolean);
    method?: string;
    as?: string;
    textClass?: string;
    iconClass?: string;
    type?: 'link' | 'separator' | 'group';
    children?: NavigationLink[];
}

export const staticNavigationLinks: NavigationLink[] = [
    {
        label: 'Home',
        icon: HomeIcon,
        route: 'dashboard',
        type: 'link',
    },
    {
        label: 'Juízes',
        icon: ShieldCheckIcon,
        route: 'users.index',
        type: 'link',
    },
    {
        label: 'Gincanas',
        icon: TrophyIcon,
        route: 'gymkhana.index',
        type: 'link',
    },
    {
        label: 'Resultados',
        icon: ChartBarIcon,
        route: 'results.index',
        type: 'link',
    },

    { type: 'separator' },
    {
        label: 'Perfil',
        icon: UserIcon,
        route: 'profile.edit',
        type: 'link',
    },
    {
        label: 'Sair',
        icon: ArrowLeftEndOnRectangleIcon,
        route: 'logout',
        method: 'post',
        as: 'button',
        textClass: 'text-red-500',
        iconClass: 'text-red-500',
        type: 'link',
    },
];