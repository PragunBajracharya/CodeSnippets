$(document).ready(function ($) {
    let current = sessionStorage.getItem("currentStep");
    let boxSize = sessionStorage.getItem("boxAmount");
    if (current !== null || current !== undefined) {
        let select = '.step[data-target="' + current + '"]';
        $(select).removeClass('disabled');
        $('.panel').removeClass('active');
        $(current).addClass('active');
        if (current === '#panel3') {
            $('.step[data-target="#panel2"]').removeClass('disabled');
        }
    }

    if (boxSize !== null || boxSize !== undefined) {
        let jsBoxSize = '.js-box-size-amount[data-value="' + boxSize + '"]';
        $('.js-box-size-amount').removeClass('active');
        $(jsBoxSize).addClass('active');
    }

    $('.step').on('click', function () {
        let step = $(this);
        let isEnabled = $(this).hasClass('disabled');
        if (!isEnabled) {
            let target = step.attr('data-target');
            $('.panel').removeClass('active');
            $(target).addClass('active');
        }
    });

    $('.btn-next').on('click', function () {
        let btn = $(this);
        let next = btn.attr('data-enable');
        let selector = '.step[data-target="' + next + '"]';
        $(selector).removeClass('disabled');
        $('.panel').removeClass('active');
        $(next).addClass('active');
        sessionStorage.setItem("currentStep", next);
    });

    $('.js-box-size-amount').on('click', function () {
        let plan = $(this);
        let value = plan.attr('data-value');
        sessionStorage.setItem("boxAmount", parseInt(value));
        $('.js-box-size-amount').removeClass('active');
        plan.addClass('active');
    });

    $('.category').on('click', function () {
        let isAll = $(this).hasClass('all');
        $('.category').removeClass('active');
        $(this).addClass('active');
        if (!isAll) {
            let collectionId = $(this).attr('data-id');
            let productList = window.productWithCategory[collectionId];
            $('.product-box').each(function () {
                let productId = $(this).attr('data-product-id');
                productId = parseInt(productId);
                if (!productList.includes(productId)) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        } else {
            $('.product-box').removeAttr('style');
        }
    });

    $('.product__updown').on('click', function () {
        let checkDec = $(this).hasClass('product__quantity-decrease');
        let checkInc = $(this).hasClass('product__quantity-increase');
        let getProductId = $(this).attr('data-slot-id');
        getProductId = parseInt(getProductId);
        let selector = 'input[data-slot-id="' + getProductId + '"]';
        let val = $(selector).val();
        if (checkInc) {
            val++;
            $(selector).val(val);
        } else if (checkDec && val > 0) {
            val--;
            $(selector).val(val);
        }
        if (val > 0) {
            $(selector).closest('.product-box').find('.product-box-inner').addClass('has-value');
        } else if (val === 0) {
            $(selector).closest('.product-box').find('.product-box-inner').removeClass('has-value');
        } else {
            $(selector).val(0);
        }
    });
});