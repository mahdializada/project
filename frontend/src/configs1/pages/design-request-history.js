
import Icons from '~/configs/menus/menuIcons.js'

export default function (appContext) {
  return {
    // Register Company Steppers
    steppers: [
      {
        icon: 'fa-question-circle',
        title: 'general',
        slotName: 'step1',
      },
      {
        icon: 'fa-lock',
        title: 'company',
        slotName: 'step2',
      },
      {
        icon: 'fa-icons',
        title: 'social_media',
        slotName: 'step3',
      },
      {
        icon: 'fa-comment-dots',
        title: 'remarks',
        slotName: 'step4',
      },
      {
        icon: 'fa-thumbs-up',
        title: 'done',
        slotName: 'step5',
      },
    ],

    // breadcrumb
    breadcrumb: [
      
			{
				text: "content_management_system",
				disabled: true,
				to: "",
				icon: Icons.content_management_system,
			},
      {
        text: 'history',
        disabled: true,
        to: '',
        icon: Icons.history,
      },
    ],

    //  tabItem
    tabItems: [
      {
        text: 'all',
        icon: 'fa-table',
        isSelected: 1,
        key: 'all',
      },

      {
        text: 'waiting',
        icon: 'fa-spinner',
        isSelected: 0,
        key: 'waiting',
      },
      {
        text: 'in_storyboard',
        icon: 'fa-hourglass-start',
        isSelected: 0,
        key: 'in storyboard',
      },
      {
        text: 'storyboard_review',
        icon: 'fa-tasks',
        isSelected: 0,
        key: 'storyboard review',
      },
      {
        text: 'storyboard_rejected',
        icon: 'fa-comment-slash',
        isSelected: 0,
        key: 'storyboard rejected',
      },
      {
        text: 'in_design',
        icon: 'fa-drafting-compass',
        isSelected: 0,
        key: 'in design',
      },
      {
        text: 'design_review',
        icon: 'fa-tasks',
        isSelected: 0,
        key: 'design review',
      },
      {
        text: 'design_rejected',
        icon: 'fa-tint-slash',
        isSelected: 0,
        key: 'design rejected',
      },
      {
        text: 'in_programming',
        icon: 'fa-code',
        isSelected: 0,
        key: 'in programming',
      },
      {
        text: 'completed',
        icon: 'fa-check',
        isSelected: 0,
        key: 'completed',
      },
      {
        text: 'cancelled',
        icon: 'fa-times',
        isSelected: 0,
        key: 'cancelled',
      },
      {
        text: 'removed',
        icon: 'fa-trash',
        isSelected: 0,
        key: 'removed',
      },
    ],

    // table headers
  };
}
