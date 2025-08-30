import { test, expect } from "@playwright/test";
import { PHPRequestHandler, PHP } from "@php-wasm/universal";
import { runCLI } from "@wp-playground/cli";
// import { login } from "@wp-playground/blueprints";
import { readFileSync } from "fs";
import { resolve } from "path";

let dfiFileName = '/wp-content/uploads/2024/01/dfi.png';
let dfiFileNameThumb = '/wp-content/uploads/2024/01/dfi-150x150.png';
let cliServer: any;
let handler: PHPRequestHandler;

test.beforeAll(async () => {
	const blueprint = JSON.parse(
		readFileSync(resolve(".wordpress-org/blueprints/blueprint.json"), "utf8")
	);
	cliServer = await runCLI({
		command: "server",
		mount: [
			{
				hostPath: "./",
				vfsPath: "/wordpress/wp-content/plugins/default-featured-image",
			},
		],
		blueprint,
		quiet: false,
	});
	handler = cliServer.requestHandler;
});

test.afterAll(async () => {
	if (cliServer) {
		await cliServer.server.close();
	}
});

test.describe("Default state", () => {
	test("Admin page shows DFI preview", async ({ page }) => {
		await page.goto(handler.absoluteUrl + '/wp-admin/options-media.php');

		const previewImg = page.locator('#preview-image img');
		await expect(previewImg).toBeVisible();
		await expect(await previewImg.getAttribute('src')).toMatch(handler.absoluteUrl + dfiFileNameThumb);

		const DFI_ID = await page.locator("#dfi_id").inputValue();
		await expect(DFI_ID).toMatch(/^\d+$/);
		await expect(DFI_ID).toBeTruthy();
	});

	test("Homepage shows posts with DFI", async ({ page }) => {
		await page.goto(handler.absoluteUrl);
		await expect(page.locator('.wp-block-group .wp-block-query ul li')).toHaveCount(7);
	});

	test("Fantasy and real animals show correct featured image", async ({ page }) => {

		// Fantasy animal: Nessi
		await page.goto(handler.absoluteUrl);
		await page.getByText('Nessi').click();
		const nessiImg = page.locator('img.wp-post-image');
		await expect(await nessiImg.getAttribute('class')).toContain('default-featured-img');
		await expect(await nessiImg.getAttribute('src')).toMatch(handler.absoluteUrl + dfiFileName);

		// Real animal: Iguana
		await page.goto(handler.absoluteUrl);
		await page.getByText('Iguana', { exact: true }).click();
		const iguanaImg = page.locator('img.wp-post-image');
		await expect(await iguanaImg.getAttribute('class')).not.toContain('default-featured-img');
		await expect(await iguanaImg.getAttribute('src')).not.toMatch(handler.absoluteUrl + dfiFileName);
	});
});


// Describe:edit
//   test:change in admin.
//   test:check for 2 on homepage.

// Describe:delete
//   test:unset in admin.
//   test:check for 0 on homepage.

// SEPERATE_FILE:Describe:deactivate, plugin
//   test:no admin setting on media page.
//   test:check for 0 on homepage.

// SEPERATE_FILE:Test rest API
//  check current value.
//  update to invalid value.
//  update to valid value.
//  check current value again.
//  unset value.
//  check current value again.
